<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Aceita apenas 11 dígitos numéricos
        if (!preg_match('/^\d{11}$/', $value)) {
            $fail('O :attribute deve conter exatamente 11 dígitos numéricos.');
            return;
        }

        // Rejeita CPFs com todos os dígitos iguais (11111111111, etc.)
        if (preg_match('/^(\d)\1{10}$/', $value)) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        // Validação do primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += (int) $value[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;

        if ((int) $value[9] !== $digito1) {
            $fail('O :attribute informado é inválido.');
            return;
        }

        // Validação do segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += (int) $value[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        if ((int) $value[10] !== $digito2) {
            $fail('O :attribute informado é inválido.');
        }
    }
}
