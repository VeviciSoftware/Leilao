<?php 

namespace App\Repositories;

use App\Http\Requests\LanceRequest;
use App\Models\Lance;
use App\Models\Leilao;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class EloquentLanceRepository implements ILanceRepository
{
    public function add(LanceRequest $request)
    {
        if (!User::find($request->usuario_id)) {
            return response()->json([
                'mensagem' => 'Usuário não encontrado'
            ], 404);
        }

        if (!Leilao::find($request->leilao_id)) {
            return response()->json([
                'mensagem' => 'O leilão não existe. Não é possível realizar lances.'
            ], 400);
        }

        if (Lance::where('leilao_id', $request->leilao_id)->max('valor') >= $request->valor) {
            return response()->json([
                'mensagem' => 'Lance deve ser maior que o lance anterior'
            ], 400);
        }

        return Lance::create($request->all());
    }
}