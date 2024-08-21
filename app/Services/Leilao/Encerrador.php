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
        if ($this->leilao->status === 'EXPIRADO') {
            $this->leilao->status = 'FINALIZADO';
            $this->leilao->save();
        } else {
            throw new \Exception('Somente leilões expirados podem ser finalizados.');
        }
    }

    public static function encerraLeiloesExpirados()
    {
        $leiloesExpirados = Leilao::where('status', 'EXPIRADO')->get();

        foreach ($leiloesExpirados as $leilao) {
            $leilao->status = 'FINALIZADO';
            $leilao->save();
        }
    }
}