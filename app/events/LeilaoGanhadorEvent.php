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

    /**
     * Create a new event instance.
     */
    public function __construct(Leilao $leilao)
    {
        $this->leilao = $leilao;
        Log::info('Evento de leilão ganhador disparado');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
