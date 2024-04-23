<?php 

namespace App\Repositories;

use App\Http\Requests\LanceRequest;
use App\Models\Lance;
use App\Models\Leilao;
use App\Models\User;

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

        //Verifica se o lance que será dado é maior que o maior lance atual
        $maiorLance = $this->getMaiorValor($request->leilao_id);
        if ($request->valor <= $maiorLance) {
            return response()->json([
                'mensagem' => 'O valor do lance deve ser maior que o maior lance atual'
            ], 400);
        }

        return Lance::create($request->all());
    }

    public function getMaiorValor(int $leilao_id)
    {
        return Lance::where('leilao_id', $leilao_id)->max('valor');
    }


}