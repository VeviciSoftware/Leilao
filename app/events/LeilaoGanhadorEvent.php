<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Leilao;
use Illuminate\Support\Facades\Log;

class LeilaoGanhadorEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $leilao;

    public function __construct(Leilao $leilao)
    {
        $this->leilao = $leilao;
        Log::info('LeilaoGanhadorEvent - Evento de leilão ganhador disparado');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
