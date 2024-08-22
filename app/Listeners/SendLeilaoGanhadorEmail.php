<?php
// app/Listeners/SendLeilaoGanhadorEmail.php

namespace App\Listeners;

use App\Events\LeilaoGanhadorEvent;
use App\Mail\LeilaoGanhadorMail;
use Illuminate\Support\Facades\Mail;

class SendLeilaoGanhadorEmail
{
    /**
     * Handle the event.
     */
    public function handle(LeilaoGanhadorEvent $event): void
    {
        $leilao = $event->leilao;
        $ganhador = $leilao->lances()->orderBy('valor', 'desc')->first()->user;

        if ($ganhador) {
            Mail::to($ganhador->email)->send(new LeilaoGanhadorMail($leilao, $ganhador));
        }
    }
}