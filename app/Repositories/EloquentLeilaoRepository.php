<?php 

namespace App\Repositories;

use App\Http\Requests\LeilaoRequest;
use App\Models\Leilao;
use App\Repositories\ILeilaoRepository;


class EloquentLeilaoRepository implements ILeilaoRepository {
    
    public function add(LeilaoRequest $request)
    {
        // Verifica se leilão já existe
        if (Leilao::where('nome', $request->nome)->first()) {
            return response()->json([
                'mensagem' => 'Leilão já existe'
            ], 400);
        }

        //Verifica se valor_inicial é maior que 0
        if ($request->valor_inicial <= 0) {
            return response()->json([
                'mensagem' => 'O valor inicial deve ser maior que 0'
            ], 400);
        }
    
        // Converte as datas para objetos DateTime
        $dataInicio = \DateTime::createFromFormat('Y-m-d H:i:s', $request->data_inicio);
        $dataTermino = \DateTime::createFromFormat('Y-m-d H:i:s', $request->data_termino);
    
        // Verifica se a data de término é maior que a data de início
        if ($dataTermino <= $dataInicio) {
            return response()->json([
                'mensagem' => 'A data de término deve ser maior que a data de início'
            ], 400);
        }
    
        $leilao = Leilao::create($request->all());
    
        return response()->json($leilao, 201);
    }

    public function getLeilaoById(int $id)
    {
        $leilao = Leilao::find($id);

        if (!$leilao) {
            return response()->json([
                'mensagem' => 'Leilão não encontrado'
            ], 404);
        }

        return response()->json($leilao, 200);
    }
}

