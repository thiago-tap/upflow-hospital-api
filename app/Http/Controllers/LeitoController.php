<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeitoResource;
use App\Http\Resources\PacienteResource;
use App\Rules\CpfValido;
use App\Services\LeitoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LeitoController extends Controller
{
    public function __construct(
        private LeitoService $leitoService
    ) {}

    /**
     * Listar todos os leitos
     *
     * Retorna a lista paginada de leitos do hospital com seus respectivos status de ocupação.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function listar(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        return LeitoResource::collection($this->leitoService->listarLeitos($perPage));
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
        $request->validate([
            'id_leito' => 'required|exists:leitos,id',
            'id_paciente' => 'required|exists:pacientes,id',
        ]);

        $this->leitoService->ocuparLeito($request->id_leito, $request->id_paciente);

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
        $request->validate([
            'id_leito' => 'required|exists:leitos,id',
        ]);

        $this->leitoService->liberarLeito($request->id_leito);

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
            'id_leito_destino' => 'required|exists:leitos,id',
        ]);

        $this->leitoService->transferirPaciente($request->id_leito_atual, $request->id_leito_destino);

        return response()->json(['mensagem' => 'Transferência realizada com sucesso.']);
    }

    /**
     * Buscar leito por CPF
     *
     * Descobre em qual leito um paciente está internado através do seu CPF.
     *
     * @param string $cpf CPF do paciente (11 dígitos numéricos)
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscarPorCpf(string $cpf)
    {
        $validator = Validator::make(['cpf' => $cpf], [
            'cpf' => ['required', 'string', new CpfValido],
        ]);

        if ($validator->fails()) {
            return response()->json(['erro' => $validator->errors()->first('cpf')], 422);
        }

        $resultado = $this->leitoService->buscarPorCpf($cpf);

        return response()->json($resultado);
    }

    /**
     * Listar todos os pacientes
     *
     * Retorna a lista paginada de todos os pacientes cadastrados no hospital.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function listarPacientes(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 100);

        return PacienteResource::collection($this->leitoService->listarPacientes($perPage));
    }
}
