<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leito extends Model
{
    protected $table = 'leitos'; // Força o nome da tabela
    protected $fillable = ['codigo', 'paciente_id'];

    // Relação: Um leito pertence a um paciente
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }
}