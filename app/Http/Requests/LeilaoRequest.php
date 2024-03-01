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
            'valor_inicial' => 'required|numeric',
            'data_inicio' => 'required|date',
            'data_termino' => 'required|date',
            'status' => 'required|in:ABERTO,FINALIZADO,EXPIRADO,INATIVO',
        ];
    }
}