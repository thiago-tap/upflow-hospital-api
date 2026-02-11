<?php

namespace App\Http\Controllers;

use App\Models\Leito;
use App\Models\Paciente;
use Illuminate\Http\Request;

class LeitoController extends Controller
{
    /**
     * Listar todos os leitos
     *
     * Retorna a lista completa de leitos do hospital com seus respectivos status de ocupação.
     *
     * @return \Illuminate\Http\JsonResponse Lista de leitos com status
     */
    public function listar()
    {
        $leitos = Leito::with('paciente')->get();

        return response()->json($leitos->map(function($leito) {
            return [
                'id_leito' => $leito->id,
                'codigo' => $leito->codigo,
                // [cite: 6] Descobrir status de ocupação
                'status' => $leito->paciente_id ? 'OCUPADO' : 'LIVRE',
                'paciente' => $leito->paciente ? $leito->paciente->nome : null
            ];
        }));
    }

    /**
     * Ocupar um leito
     *
     * Interna um paciente em um leito específico. O leito deve estar livre e o paciente não pode estar em outro leito.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ocupar(Request $request)
    {
        // Validação simples
        $request->validate([
            'id_leito' => 'required|exists:leitos,id',
            'id_paciente' => 'required|exists:pacientes,id'
        ]);

        $leito = Leito::find($request->id_leito);

        // Regra: Leito já tem gente?
        if ($leito->paciente_id) {
            return response()->json(['erro' => 'Este leito já está ocupado.'], 400);
        }

        // Regra: Paciente já está internado em outro lugar?
        $pacienteOcupado = Leito::where('paciente_id', $request->id_paciente)->exists();
        if ($pacienteOcupado) {
            return response()->json(['erro' => 'Este paciente já está ocupando outro leito.'], 400);
        }

        $leito->paciente_id = $request->id_paciente;
        $leito->save();

        return response()->json(['mensagem' => 'Paciente internado com sucesso.']);
    }

    /**
     * Liberar um leito
     *
     * Desocupa um leito, removendo o paciente que estava internado.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function liberar(Request $request)
    {
        $request->validate(['id_leito' => 'required|exists:leitos,id']);

        $leito = Leito::find($request->id_leito);
        $leito->paciente_id = null; // Esvazia o leito
        $leito->save();

        return response()->json(['mensagem' => 'Leito liberado com sucesso.']);
    }

    /**
     * Transferir paciente
     *
     * Transfere um paciente de um leito para outro. O leito de origem deve ter um paciente e o leito de destino deve estar livre.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferir(Request $request)
    {
        $request->validate([
            'id_leito_atual' => 'required|exists:leitos,id',
            'id_leito_destino' => 'required|exists:leitos,id'
        ]);

        $leitoAtual = Leito::find($request->id_leito_atual);
        $leitoDestino = Leito::find($request->id_leito_destino);

        if (!$leitoAtual->paciente_id) {
            return response()->json(['erro' => 'Não há paciente no leito de origem para transferir.'], 400);
        }

        if ($leitoDestino->paciente_id) {
            return response()->json(['erro' => 'O leito de destino já está ocupado.'], 400);
        }

        // Realiza a transferência
        // Primeiro libera o leito atual para evitar violação da constraint UNIQUE
        $pacienteId = $leitoAtual->paciente_id;
        $leitoAtual->paciente_id = null;
        $leitoAtual->save();

        // Depois ocupa o leito de destino
        $leitoDestino->paciente_id = $pacienteId;
        $leitoDestino->save();

        return response()->json(['mensagem' => 'Transferência realizada com sucesso.']);
    }

    /**
     * Buscar leito por CPF
     *
     * Descobre em qual leito um paciente está internado através do seu CPF.
     *
     * @param string $cpf CPF do paciente (formato: XXX.XXX.XXX-XX)
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarPorCpf($cpf)
    {
        $paciente = Paciente::where('cpf', $cpf)->with('leito')->first();

        if (!$paciente) {
            return response()->json(['erro' => 'Paciente não encontrado.'], 404);
        }

        if (!$paciente->leito) {
            return response()->json(['mensagem' => 'O paciente não está internado no momento.']);
        }

        return response()->json([
            'paciente' => $paciente->nome,
            'leito' => $paciente->leito->codigo,
            'status' => 'OCUPADO'
        ]);
    }
}
