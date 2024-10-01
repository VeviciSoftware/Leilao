<?php

namespace Tests\Integration\Database;

use Tests\TestCase;
use App\Models\Leilao;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeilaoTest extends TestCase 
{
    use RefreshDatabase;

    /**
     * @dataProvider leiloes
    */
    public function testListagemDeLeiloes(array $leiloes): void
    {
        foreach ($leiloes as $leilao) {
            $leilao->save();
        }
    
        $leiloesDoBanco = Leilao::all();
    
        $this->assertCount(count($leiloes), $leiloesDoBanco);
    
        foreach ($leiloes as $index => $leilao) {
            $this->assertEquals($leilao->nome, $leiloesDoBanco[$index]->nome);
            $this->assertEquals($leilao->descricao, $leiloesDoBanco[$index]->descricao);
            $this->assertEquals($leilao->valor_inicial, $leiloesDoBanco[$index]->valor_inicial);
            $this->assertEquals($leilao->data_inicio, $leiloesDoBanco[$index]->data_inicio);
            $this->assertEquals($leilao->data_termino, $leiloesDoBanco[$index]->data_termino);
            $this->assertEquals($leilao->status, $leiloesDoBanco[$index]->status);
            $this->assertEquals($leilao->leilao_ganhador, $leiloesDoBanco[$index]->leilao_ganhador);
            $this->assertEquals($leilao->usuario_id, $leiloesDoBanco[$index]->usuario_id);
        }
    }

    /**
     * @dataProvider leiloes
    */
    public function testListagemDeLeiloesComFiltroPorStatus(array $leiloes): void{
       //Arrange
       foreach ($leiloes as $leilao) {
           $leilao->save();
        }

        //Act
        $leiloesAbertos = Leilao::where('status', 'ABERTO')->get();
        $leiloesFinalizados = Leilao::where('status', 'FINALIZADO')->get();

        //Assert
        $this->assertCount(2, $leiloesAbertos);
        $this->assertCount(1, $leiloesFinalizados);
    }

    public static function leiloes(): array
    {
        $leilao1 = new Leilao([
            'nome' => 'Leilão 1', 
            'descricao' => 'Descrição do Leilão 1', 
            'valor_inicial' => 100, 
            'data_inicio' => '2023-01-01 00:00:00', 
            'data_termino' => '2023-01-10 00:00:00', 
            'status' => 'ABERTO', 
            'leilao_ganhador' => null, 
            'usuario_id' => 1
        ]);
    
        $leilao2 = new Leilao([
            'nome' => 'Leilão 2', 
            'descricao' => 'Descrição do Leilão 2', 
            'valor_inicial' => 200, 
            'data_inicio' => '2023-02-01 00:00:00', 
            'data_termino' => '2023-02-10 00:00:00', 
            'status' => 'ABERTO', 
            'leilao_ganhador' => null, 
            'usuario_id' => 2
        ]);
    
        $leilao3 = new Leilao([
            'nome' => 'Leilão 3', 
            'descricao' => 'Descrição do Leilão 3', 
            'valor_inicial' => 300, 
            'data_inicio' => '2023-03-01 00:00:00', 
            'data_termino' => '2023-03-10 00:00:00', 
            'status' => 'FINALIZADO', 
            'leilao_ganhador' => null, 
            'usuario_id' => 3
        ]);
    
        return [
            [[$leilao1, $leilao2, $leilao3]],
        ];
    }
}
