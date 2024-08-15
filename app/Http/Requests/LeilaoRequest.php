<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeilaoRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string|max:500',
            'valor_inicial' => 'required|numeric|min:0.01',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date',
            'status' => 'required|string|in:ABERTO,FINALIZADO,EXPIRADO,INATIVO'
        ];
    }

    public function messages() {
        return [
            'nome.required' => 'O campo nome é obrigatório',
            'nome.string' => 'O campo nome deve ser uma string',
            'nome.max' => 'O campo nome deve ter no máximo 255 caracteres',
            'descricao.required' => 'O campo descrição é obrigatório',
            'descricao.string' => 'O campo descrição deve ser uma string',
            'descricao.max' => 'O campo descrição deve ter no máximo 500 caracteres',
            'valor_inicial.required' => 'O campo valor inicial é obrigatório',
            'valor_inicial.numeric' => 'O campo valor inicial deve ser um número',
            'valor_inicial.min' => 'O campo valor inicial deve ser maior que 0',
            'data_inicio.required' => 'O campo data de início é obrigatório',
            'data_inicio.date' => 'O campo data de início deve ser uma data',
            'data_termino.required' => 'O campo data de término é obrigatório',
            'data_termino.date' => 'O campo data de término deve ser uma data',
            'status.required' => 'O campo status é obrigatório',
            'status.string' => 'O campo status deve ser uma string',
            'status.in' => 'O campo status deve ser um dos valores: ABERTO, FINALIZADO, EXPIRADO, INATIVO'
        ];
    }
}