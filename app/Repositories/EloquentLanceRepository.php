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

        //Verifica se o valor do lance é maior que zero
        if ($request->valor <= 0) {
            return response()->json([
                'mensagem' => 'O valor do lance deve ser maior que zero'
            ], 400);
        }

        //Verifica se o lance que será dado é maior que o maior lance atual
        $maiorLance = $this->getMaiorValorLance($request->leilao_id);
        if ($request->valor <= $maiorLance) {
            return response()->json([
                'mensagem' => 'O valor do lance deve ser maior que o maior lance atual'
            ], 400);
        }

        //Verifica se o lance que será dado é maior que o valor inicial do leilão
        $lanceMinimo = $this->getLanceMinimoLeilao($request->leilao_id);
        if ($request->valor < $lanceMinimo) {
            return response()->json([
                'mensagem' => 'O valor do lance deve ser maior ou igual ao valor inicial do leilão'
            ], 400);
        }

        //Verifica se o usuário está dando dois lances seguidos. Se sim, não é permitido.
        $lanceAnterior = Lance::where('usuario_id', $request->usuario_id)->orderBy('created_at', 'desc')->first();
        if ($lanceAnterior) {
            if ($lanceAnterior->leilao_id == $request->leilao_id) {
                return response()->json([
                    'mensagem' => 'O usuário não pode dar dois lances seguidos'
                ], 400);
            }
        }

        return Lance::create($request->all());
    }

    public function getMaiorValorLance(int $leilao_id)
    {
        return Lance::where('leilao_id', $leilao_id)->max('valor');
    }

    public function getLanceMinimoLeilao(int $leilao_id)
    {
        $leilao = Leilao::find($leilao_id);
        return $leilao->valor_inicial;
    }

}