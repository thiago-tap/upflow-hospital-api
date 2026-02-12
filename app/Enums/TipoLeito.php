<?php

namespace App\Enums;

enum TipoLeito: string
{
    case UTI = 'UTI';
    case ENFERMARIA = 'ENFERMARIA';
    case QUARTO = 'QUARTO';
}