<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CpfRule;

class UserCompleteRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cpf' => preg_replace('/[^0-9]/', "", $this->cpf), // Remove tudo que não for número
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "cpf" => ['required', 'string', 'size:11', new CpfRule()],
            "name" =>  ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            "birthday" => ["required","date","before:today"]
        ];
    }
}
