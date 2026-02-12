<?php

namespace App\Exceptions;

use Exception;

class LeitoException extends Exception
{
    public static function indisponivel(): self
    {
        return new self('Este leito não está disponível (Ocupado ou em Manutenção).', 400);
    }

    public static function pacienteJaInternado(): self
    {
        return new self('Este paciente já está ocupando outro leito.', 400);
    }

    public static function semPacienteNaOrigem(): self
    {
        return new self('Não há paciente no leito de origem para transferir.', 400);
    }

    public static function destinoOcupado(): self
    {
        return new self('O leito de destino já está ocupado.', 400);
    }

    public static function pacienteNaoEncontrado(): self
    {
        return new self('Paciente não encontrado.', 404);
    }
}
