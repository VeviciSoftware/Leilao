<?php
namespace App\Services\Leilao;

use App\Models\Leilao;
use App\Events\LeilaoGanhadorEvent;
use Illuminate\Support\Facades\Log;

class LeilaoService
{
    public function finalizaLeilao($id)
    {
        try {
            Log::info('Iniciando finalização do leilão', ['leilao_id' => $id]);

            $leilao = Leilao::find($id);

            if (!$leilao) {
                Log::warning('Leilão não encontrado', ['leilao_id' => $id]);
                return ['status' => 404, 'mensagem' => 'Leilão não encontrado'];
            }

            $lanceVencedor = $leilao->lances()->orderBy('valor', 'desc')->first();

            if ($lanceVencedor) {
                Log::info('Lance vencedor encontrado', ['user_id' => $lanceVencedor->usuario_id]);
                $leilao->leilao_ganhador = $lanceVencedor->usuario_id;
            } else {
                Log::info('Nenhum lance vencedor encontrado', ['leilao_id' => $id]);
            }

            if (!$leilao->isAberto() && !$leilao->isExpirado()) {
                Log::warning('Tentativa de finalizar leilão com status inválido', ['leilao_id' => $id, 'status' => $leilao->status]);
                return ['status' => 400, 'mensagem' => 'Apenas leilões com status ABERTO e EXPIRADO podem ser FINALIZADOS. Status do leilão: ' . $leilao->status];
            }

            $leilao->status = 'FINALIZADO';
            $leilao->save();

            if ($lanceVencedor && $lanceVencedor->participante) {
                event(new LeilaoGanhadorEvent($leilao));
                Log::info('Evento de e-mail disparado para o ganhador do leilão', ['user_id' => $lanceVencedor->usuario_id]);
                return ['status' => 200, 'mensagem' => 'Parabéns ' . $lanceVencedor->participante->name . ', você ganhou o leilão. Confira sua caixa de e-mail.'];
            }

            return ['status' => 200, 'mensagem' => 'Leilão encerrado com sucesso'];
        } catch (\Exception $e) {
            Log::error('Erro ao finalizar leilão', ['error' => $e->getMessage(), 'leilao_id' => $id]);
            return ['status' => 500, 'mensagem' => 'Ocorreu um erro ao finalizar o leilão. Por favor, tente novamente mais tarde.'];
        }
    }
}