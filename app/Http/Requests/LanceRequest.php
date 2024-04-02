<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [     
            'valor' => 'required|numeric',
            'leilao_id' => 'required|numeric',
            'usuario_id' => 'required|numeric',
        ];
    }
}
