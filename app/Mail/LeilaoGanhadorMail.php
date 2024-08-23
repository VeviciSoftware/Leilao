<?php

namespace App\Mail;

use App\Models\Leilao;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;

class LeilaoGanhadorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leilao;
    public $ganhador;

    public function __construct(Leilao $leilao, User $ganhador)
    {
        $this->leilao = $leilao;
        $this->ganhador = $ganhador;
    }

    public function build()
    {
        Log::info("LeilaoGanhadorMail - Construindo e-mail para o ganhador do leilão: {$this->ganhador->email}");
        
        try {
            return $this->markdown('mail.leilao_ganhador');
        } catch (\Exception $e) {
            Log::error("Erro ao construir o e-mail: {$e->getMessage()}");
        }
    }
}