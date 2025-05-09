<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCPF implements Rule
{
    public function passes($attribute, $value)
    {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $value);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }
        
        // Validação básica de CPF (pode implementar algoritmo completo depois)
        return true;
    }

    public function message()
    {
        return 'O CPF informado é inválido.';
    }
}
