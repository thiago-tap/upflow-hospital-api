<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes'; // Força o nome da tabela
    protected $fillable = ['nome', 'cpf'];

    // Relação: Um paciente tem um leito
    public function leito()
    {
        return $this->hasOne(Leito::class, 'paciente_id');
    }
}