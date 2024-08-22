<?php

namespace App\Mail;

use App\Models\Leilao;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeilaoGanhadorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leilao;
    public $ganhador;

    /**
     * Create a new message instance.
     */
    public function __construct(Leilao $leilao, User $ganhador)
    {
        $this->leilao = $leilao;
        $this->ganhador = $ganhador;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Parabéns pelo arremate!')
                    ->view('emails.leilao_ganhador')
                    ->with([
                        'leilao' => $this->leilao,
                        'ganhador' => $this->ganhador,
                    ]);
    }
}