<?php
namespace App\Listeners;

use App\Events\LeilaoGanhadorEvent;
use App\Mail\LeilaoGanhadorMail;
use Illuminate\Support\Facades\Mail;
use Log;

class SendLeilaoGanhadorEmail
{
    public function handle(LeilaoGanhadorEvent $event): void
    {
        Log::info('Entrou no listener SendLeilaoGanhadorEmail - Enviando e-mail para o ganhador do leilão');

        $leilao = $event->leilao;
        $ganhador = $leilao->lances()->orderBy('valor', 'desc')->first()->participante;
        //dd($ganhador);

        if ($ganhador) {
            try {
                Log::info("Tentando enviar e-mail para: {$ganhador->email}");
                Mail::to($ganhador->email)->send(new LeilaoGanhadorMail($leilao, $ganhador));
                Log::info("E-mail enviado para o ganhador do leilão: {$ganhador->email}");
            } catch (\Exception $e) {
                Log::error("Erro ao enviar e-mail para o ganhador do leilão: {$ganhador->email}", ['error' => $e->getMessage()]);
            }
        } else {
            Log::warning("Nenhum ganhador encontrado para o leilão: {$leilao->id}");
        }
    }
}