<?php

namespace App\Services;

use App\Enums\StatusLeito;
use App\Exceptions\LeitoException;
use App\Models\AuditoriaLeito;
use App\Models\Leito;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeitoService
{
    public function listarLeitos(int $perPage = 15)
    {
        return Leito::with('paciente')->paginate($perPage);
    }

    public function ocuparLeito(int $idLeito, int $idPaciente): void
    {
        $leito = Leito::findOrFail($idLeito);

        if ($leito->paciente_id || $leito->status !== StatusLeito::LIVRE) {
            throw LeitoException::indisponivel();
        }

        if (Leito::where('paciente_id', $idPaciente)->exists()) {
            throw LeitoException::pacienteJaInternado();
        }

        DB::transaction(function () use ($leito, $idPaciente) {
            $leito->paciente_id = $idPaciente;
            $leito->status = StatusLeito::OCUPADO;
            $leito->save();

            $this->registrarAuditoria($leito->id, $idPaciente, 'OCUPAR', "Paciente internado no leito {$leito->codigo}");
        });
    }

    public function liberarLeito(int $idLeito): void
    {
        $leito = Leito::findOrFail($idLeito);
        $pacienteId = $leito->paciente_id;

        $leito->paciente_id = null;
        $leito->status = StatusLeito::LIVRE;
        $leito->save();

        $this->registrarAuditoria($leito->id, $pacienteId, 'LIBERAR', "Leito {$leito->codigo} liberado");
    }

    public function transferirPaciente(int $idLeitoAtual, int $idLeitoDestino): void
    {
        $leitoAtual = Leito::findOrFail($idLeitoAtual);
        $leitoDestino = Leito::findOrFail($idLeitoDestino);

        if (!$leitoAtual->paciente_id) {
            throw LeitoException::semPacienteNaOrigem();
        }

        if ($leitoDestino->paciente_id || $leitoDestino->status !== StatusLeito::LIVRE) {
            throw LeitoException::destinoOcupado();
        }

        DB::transaction(function () use ($leitoAtual, $leitoDestino) {
            $pacienteId = $leitoAtual->paciente_id;

            $leitoAtual->paciente_id = null;
            $leitoAtual->status = StatusLeito::LIVRE;
            $leitoAtual->save();

            $leitoDestino->paciente_id = $pacienteId;
            $leitoDestino->status = StatusLeito::OCUPADO;
            $leitoDestino->save();

            $this->registrarAuditoria(
                $leitoDestino->id,
                $pacienteId,
                'TRANSFERIR',
                "Paciente transferido do leito {$leitoAtual->codigo} para {$leitoDestino->codigo}"
            );
        });
    }

    public function buscarPorCpf(string $cpf): array
    {
        $paciente = Paciente::where('cpf', $cpf)->with('leito')->first();

        if (!$paciente) {
            throw LeitoException::pacienteNaoEncontrado();
        }

        if (!$paciente->leito) {
            return ['mensagem' => 'O paciente não está internado no momento.'];
        }

        return [
            'paciente' => $paciente->nome,
            'leito' => $paciente->leito->codigo,
            'tipo' => $paciente->leito->tipo,
            'status' => $paciente->leito->status,
        ];
    }

    public function listarPacientes(int $perPage = 15)
    {
        return Paciente::paginate($perPage);
    }

    private function registrarAuditoria(?int $leitoId, ?int $pacienteId, string $acao, string $detalhes): void
    {
        AuditoriaLeito::create([
            'leito_id' => $leitoId,
            'paciente_id' => $pacienteId,
            'acao' => $acao,
            'detalhes' => $detalhes,
        ]);

        Log::info("Auditoria Leito: [{$acao}] {$detalhes}", [
            'leito_id' => $leitoId,
            'paciente_id' => $pacienteId,
        ]);
    }
}
