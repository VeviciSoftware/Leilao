<?php 

namespace App\Services\Leilao;

use App\Models\Leilao;

class Encerrador
{
    private $leilao;

    public function __construct(Leilao $leilao)
    {
        $this->leilao = $leilao;
    }

    public function encerra()
    {
        $this->leilao->finalizado = true;
        $this->leilao->save();
    }
}