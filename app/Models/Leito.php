<?php

namespace App\Models;

use App\Enums\StatusLeito;
use App\Enums\TipoLeito;
use Illuminate\Database\Eloquent\Model;

class Leito extends Model
{
    protected $table = 'leitos'; // Força o nome da tabela
    protected $fillable = ['codigo', 'tipo', 'status', 'paciente_id'];

    protected $casts = [
        'status' => StatusLeito::class,
        'tipo' => TipoLeito::class,
    ];

    // Relação: Um leito pertence a um paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}
