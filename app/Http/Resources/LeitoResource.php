<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeitoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_leito' => $this->id,
            'codigo' => $this->codigo,
            'tipo' => $this->tipo,
            'status' => $this->status,
            'paciente' => $this->whenLoaded('paciente', function () {
                return new PacienteResource($this->paciente);
            }),
        ];
    }
}
