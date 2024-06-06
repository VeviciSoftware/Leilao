<?php 

namespace App\Repositories;

use App\Http\Requests\LeilaoRequest;
use App\Models\Leilao;
use App\Repositories\ILeilaoRepository;


class EloquentLeilaoRepository implements ILeilaoRepository {
    
    public function add(LeilaoRequest $request)
    {
        //Verifica se leilão já existe
        if (Leilao::where('nome', $request->nome)->first()) {
            return response()->json([
                'mensagem' => 'Leilão já existe'
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

