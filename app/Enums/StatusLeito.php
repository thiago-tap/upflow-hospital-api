<?php

namespace App\Enums;

enum StatusLeito: string
{
    case LIVRE = 'LIVRE';
    case OCUPADO = 'OCUPADO';
    case MANUTENCAO = 'MANUTENCAO';
}
