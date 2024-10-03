<?php

namespace Tests\Integration\Database;

use App\Models\Lance;
use App\Models\User;
use App\Services\Leilao\Encerrador;
use App\Services\Leilao\LeilaoService;
use Tests\TestCase;
use App\Models\Leilao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

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
    public function testListagemDeLeiloesComFiltroPorStatus(array $leiloes): void
    {
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

    /**
     * @dataProvider leilaoInativo
     */
    public function testCadastroLeilaoInativo(Leilao $leilao): void
    {
        $leilao->save();

        $leilaoDoBanco = Leilao::find($leilao->id);

        $this->assertTrue($leilaoDoBanco->isInativo());
    }

    /**
     * @dataProvider leilaoAberto
     */
    public function testCadastroLeilaoAberto(Leilao $leilao): void
    {
        $leilao->save();

        $leilaoDoBanco = Leilao::find($leilao->id);

        $this->assertTrue($leilaoDoBanco->isAberto());
    }

    /**
     * @dataProvider leilaoAberto
     */
    public function testLeilaoAbertoEstaAptoParaReceberLances(Leilao $leilao): void
    {
        // Arrange
        $leilao->save();

        $user = new User(
            [
                'name' => 'João',
                'email' => 'joao@email.com',
                'password' => bcrypt('12345678')
            ]
        );
        $user->save();

        $lance = new Lance(
            [
                'valor' => 600,
                'leilao_id' => $leilao->id,
                'usuario_id' => $user->id
            ]
        );

        // Act
        $leilao->lances()->save($lance);

        // Assert
        $this->assertTrue($leilao->isAberto());
        $this->assertCount(1, $leilao->lances);
        $this->assertEquals(600, $leilao->lances->first()->valor);
    }

    /**
     * @dataProvider leilaoAberto
     */
    public function testLeilaoExpiraComBaseNaDataDeExpiracao(Leilao $leilao): void
    {
        // Arrange
        $leilao->save();

        // Simula a passagem do tempo para uma data após a data de término do leilão
        Carbon::setTestNow(Carbon::parse('2024-10-11 00:00:00'));

        // Atualiza o status do leilão para EXPIRADO se a data atual for maior que a data de término
        if (Carbon::now()->greaterThan(Carbon::parse($leilao->data_termino))) {
            $leilao->status = 'EXPIRADO';
            $leilao->save();
        }

        // Act
        $leilaoDoBanco = Leilao::find($leilao->id);

        // Assert
        $this->assertTrue($leilaoDoBanco->isExpirado());

        // Restaura a data atual
        Carbon::setTestNow();
    }

    /**
     * @dataProvider leilaoExpirado
     */
    public function testLeilaoExpiradoDeveSerFinalizado(Leilao $leilao)
    {
        // Arrange
        $leilao->save();
        $encerrador = new Encerrador($leilao);

        // Act
        $encerrador->encerra();

        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertTrue($leilaoDoBanco->isFinalizado());

    }


    /** 
     * @dataProvider leilaoAberto
     */
    public function testLeilaoAbertoPodeSerFinalizado(Leilao $leilao)
    {
        // Arrange
        $leilao->save();
        $encerrador = new Encerrador($leilao);

        // Act
        $encerrador->encerra();

        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertTrue($leilaoDoBanco->isFinalizado());
        $this->assertFalse($leilaoDoBanco->isAberto());
    }

    /** 
     * @dataProvider leilaoInativo
     */
    public function testLeilaoInativoNaoPodeSerFinalizado(Leilao $leilao): void
    {
        // Arrange
        $leilao->save();
        $encerrador = new Encerrador($leilao);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Somente leilões expirados ou abertos podem ser finalizados.');

        // Act
        $encerrador->encerra();

        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertTrue($leilaoDoBanco->isInativo());
        $this->assertFalse($leilaoDoBanco->isFinalizado());
    }

    public function testUsuarioQueDeuOMaiorLanceDeveSerGanhador()
    {
        // Arrange
        $leilao = new Leilao([
            'nome' => 'Leilão 1',
            'descricao' => 'Descrição do Leilão 1',
            'valor_inicial' => 100,
            'data_inicio' => '2023-01-01 00:00:00',
            'data_termino' => '2023-01-10 00:00:00',
            'status' => 'ABERTO',
            'leilao_ganhador' => null,
            'usuario_id' => 1
        ]);
    
        $leilao->save();
    
        $user1 = new User([
            'name' => 'José',
            'email' => 'joao@email.com',
            'password' => bcrypt('12345678')
        ]);
        $user1->save();
    
        $user2 = new User([
            'name' => 'Maria',
            'email' => 'maria@email.com',
            'password' => bcrypt('12345678')
        ]);
        $user2->save();
    
        $lance1 = new Lance([
            'valor' => 150,
            'leilao_id' => $leilao->id,
            'usuario_id' => $user1->id
        ]);
    
        $lance2 = new Lance([
            'valor' => 200,
            'leilao_id' => $leilao->id,
            'usuario_id' => $user2->id
        ]);
    
        $leilao->lances()->save($lance1);
        $leilao->lances()->save($lance2);
    
        // Act
        $leilaoService = new LeilaoService();
        $leilaoService->finalizaLeilao($leilao->id);
    
        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertEquals($user2->id, $leilaoDoBanco->leilao_ganhador);
        $this->assertEquals('FINALIZADO', $leilaoDoBanco->status);
        $this->assertCount(2, $leilaoDoBanco->lances);
        $this->assertEquals(150, $leilaoDoBanco->lances->first()->valor);
        $this->assertEquals(200, $leilaoDoBanco->lances->last()->valor);
    }

    public function testCreateLeilao()
    {
        $nome = 'Leilão de Arte';
        $descricao = 'Leilão de obras de arte';
        $valor_inicial = 1000.00;
        $data_inicio = '2023-10-01 10:00:00';
        $data_termino = '2023-10-10 18:00:00';
        $status = 'ABERTO';
        $leilao_ganhador = null;

        // Arrange
        $leilao = new Leilao([
            'nome' => $nome,
            'descricao' => $descricao,
            'valor_inicial' => $valor_inicial,
            'data_inicio' => $data_inicio,
            'data_termino' => $data_termino,
            'status' => $status,
            'leilao_ganhador' => $leilao_ganhador,
        ]);

        // Act
        $leilao->save();

        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertEquals($nome, $leilaoDoBanco->nome);
        $this->assertEquals($descricao, $leilaoDoBanco->descricao);
        $this->assertEquals($valor_inicial, $leilaoDoBanco->valor_inicial);
        $this->assertEquals($data_inicio, $leilaoDoBanco->data_inicio);
        $this->assertEquals($data_termino, $leilaoDoBanco->data_termino);
        $this->assertEquals($status, $leilaoDoBanco->status);
        $this->assertEquals($leilao_ganhador, $leilaoDoBanco->leilao_ganhador);
    }

    public function testEditLeilao() 
    {
        // Arrange
        $leilao = new Leilao([
            'nome' => 'Leilão de Arte',
            'descricao' => 'Leilão de obras de arte',
            'valor_inicial' => 1000.00,
            'data_inicio' => '2023-10-01 10:00:00',
            'data_termino' => '2023-10-10 18:00:00',
            'status' => 'ABERTO',
            'leilao_ganhador' => null,
        ]);
        $leilao->save();

        // Act
        $leilao->nome = 'Leilão de Arte Moderna';
        $leilao->descricao = 'Leilão de obras de arte moderna';
        $leilao->valor_inicial = 2000.00;
        $leilao->data_inicio = '2023-10-01 10:00:00';
        $leilao->data_termino = '2023-10-10 18:00:00';
        $leilao->status = 'ABERTO';
        $leilao->leilao_ganhador = null;
        $leilao->save();

        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertEquals('Leilão de Arte Moderna', $leilaoDoBanco->nome);
        $this->assertEquals('Leilão de obras de arte moderna', $leilaoDoBanco->descricao);
        $this->assertEquals(2000.00, $leilaoDoBanco->valor_inicial);
        $this->assertEquals('2023-10-01 10:00:00', $leilaoDoBanco->data_inicio);
        $this->assertEquals('2023-10-10 18:00:00', $leilaoDoBanco->data_termino);
        $this->assertEquals('ABERTO', $leilaoDoBanco->status);
        $this->assertEquals(null, $leilaoDoBanco->leilao_ganhador);
    }

    /**
     * @dataProvider leilaoAberto
     */
    public function testDeleteLeilao(Leilao $leilao)
    {
        // Arrange
        $leilao->save();

        // Act
        $leilao->delete();

        // Assert
        $leilaoDoBanco = Leilao::find($leilao->id);
        $this->assertNull($leilaoDoBanco);
    }

    /**
     * @dataProvider leilaoAberto
     */
    public function testGetLeilao(Leilao $leilao): void
    {
        // Arrange
        $leilao->save();
    
        // Act
        $leilaoDoBanco = Leilao::find($leilao->id);
    
        // Assert
        $this->assertEquals('Leilão 5', $leilaoDoBanco->nome);
        $this->assertEquals('Descrição do Leilão 5', $leilaoDoBanco->descricao);
        $this->assertEquals(500, $leilaoDoBanco->valor_inicial);
        $this->assertEquals('2023-05-01 00:00:00', $leilaoDoBanco->data_inicio);
        $this->assertEquals('2023-05-10 00:00:00', $leilaoDoBanco->data_termino);
        $this->assertEquals('ABERTO', $leilaoDoBanco->status);
        $this->assertEquals(null, $leilaoDoBanco->leilao_ganhador);
    }

    /**
     * @dataProvider leiloes
     */
    public function testGetLeiloesDoBanco(array $leiloes): void
    {
        // Arrange
        foreach ($leiloes as $leilao) {
            $leilao->save();
        }

        // Act
        $leiloesDoBanco = Leilao::all();

        // Assert
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

    public static function leilaoInativo(): array
    {
        $leilao = new Leilao([
            'nome' => 'Leilão 4',
            'descricao' => 'Descrição do Leilão 4',
            'valor_inicial' => 400,
            'data_inicio' => '2023-04-01 00:00:00',
            'data_termino' => '2023-04-10 00:00:00',
            'status' => 'INATIVO',
            'leilao_ganhador' => null,
            'usuario_id' => 4
        ]);

        return [
            [$leilao],
        ];
    }

    public static function leilaoAberto()
    {
        $leilao = new Leilao([
            'nome' => 'Leilão 5',
            'descricao' => 'Descrição do Leilão 5',
            'valor_inicial' => 500,
            'data_inicio' => '2023-05-01 00:00:00',
            'data_termino' => '2023-05-10 00:00:00',
            'status' => 'ABERTO',
            'leilao_ganhador' => null,
            'usuario_id' => 1
        ]);

        return [
            [$leilao],
        ];
    }

    public static function leilaoExpirado()
    {
        $leilao = new Leilao([
            'nome' => 'Leilão 6',
            'descricao' => 'Descrição do Leilão 6',
            'valor_inicial' => 600,
            'data_inicio' => '2023-06-01 00:00:00',
            'data_termino' => '2023-06-10 00:00:00',
            'status' => 'EXPIRADO',
            'leilao_ganhador' => null,
            'usuario_id' => 1
        ]);

        return [
            [$leilao],
        ];
    }


}
