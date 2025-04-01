<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Existe a biblioteca pt-br-validator
 * Mas como é só pro CPF, pensei em utilizar uma implementação própria
 * e demonstrar o uso das Rules customizadas do Laravel
 */
class CpfRule implements ValidationRule
{
    
    /**
     * Valida o código verificador do CPF (o código que fica depois do hífen)
     * @param string $cpf
     * @return bool
     */
    private function validateVerifierCodeCpf($cpf) {   
        /**
         * Esse código é uma cópia desse link
         * https://github.com/hcodebr/blog-php/blob/master/Fun%C3%A7%C3%A3o%20valida%C3%A7%C3%A3o%20CPF/index.php
         */ 
        $number_quantity_to_loop = [9, 10];
        foreach ($number_quantity_to_loop as $item) {
            $sum = 0;
            $number_to_multiplicate = $item + 1;
        
            for ($index = 0; $index < $item; $index++) {
                $sum += $cpf[$index] * ($number_to_multiplicate--);
            }
    
            $result = (($sum * 10) % 11);
    
            if ($cpf[$item] != $result) return false;
        }
    
        return true;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $cpf, Closure $fail): void
    {
        //Caso essa Rule seja usada em contextos em que acabe vindo com hífens
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        $only_has_equal_chars =  preg_match('/(\d)\1{10}/', $cpf);
        if (
            strlen($cpf) !== 11 || 
            $only_has_equal_chars || 
            !$this->validateVerifierCodeCpf($cpf)
        ) {
            $fail('The provided CPF is invalid');
            return;
        }
    }
}
