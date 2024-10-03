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
        try {
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
    
            // Verifica se o valor do lance é maior que zero
            if ($request->valor <= 0) {
                return response()->json([
                    'mensagem' => 'O valor do lance deve ser maior que zero'
                ], 400);
            }
    
            // A lógica de validação foi movida para o modelo Lance
            return Lance::create($request->all());
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'mensagem' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'mensagem' => 'Ocorreu um erro ao processar o lance. Por favor, tente novamente mais tarde.'
            ], 500);
        }
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