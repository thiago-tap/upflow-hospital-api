<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaLeito extends Model
{
    protected $table = 'auditoria_leitos';

    protected $fillable = ['leito_id', 'paciente_id', 'acao', 'detalhes'];

    public function leito()
    {
        return $this->belongsTo(Leito::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}
