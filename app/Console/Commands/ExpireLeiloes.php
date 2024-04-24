<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Leilao;

class ExpireLeiloes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leiloes:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica se os leilões estão expirados e finaliza-os';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leiloes = Leilao::where('status', 'ABERTO')
            ->where('data_termino', '<', now())
            ->get();

        foreach ($leiloes as $leilao) {
            $leilao->status = 'FINALIZADO';
            $leilao->save();
        }
    }
}
