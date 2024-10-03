<?php

namespace Tests\Integration\Database;

use App\Models\Lance;
use App\Models\Leilao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LancesTest extends TestCase
{
    use RefreshDatabase;

    public function testLanceNuncaPodeAssumirValorMenorQueOValorInicialDeUmLeilao()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->status = 'ABERTO';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();

        $user = new User();
        $user->name = 'Fulano';
        $user->email = 'fulano@email';
        $user->password = bcrypt('12345678');
        $user->save();

        // Act
        $exceptionMessage = '';
        try {
            $lance = new Lance();
            $lance->leilao_id = $leilao->id;
            $lance->usuario_id = $user->id;
            $lance->valor = 999;
            $lance->save();
            
        } catch (\Exception $e) {
            $this->assertTrue(true);
            $exceptionMessage = $e->getMessage();
        }

        // Assert
        $this->assertCount(0, Lance::all());
        $this->assertEquals('O valor do lance não pode ser menor que o valor inicial do leilão.', $exceptionMessage);
    }

    public function testLanceNuncaPodeAssumirValorMenorOuIgualAoValorDoUltimoLance()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->status = 'ABERTO';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();
    
        $user = new User();
        $user->name = 'Fulano';
        $user->email = 'fulano@email';
        $user->password = bcrypt('12345678');
        $user->save();
    
        $lance = new Lance();
        $lance->leilao_id = $leilao->id;
        $lance->usuario_id = $user->id;
        $lance->valor = 1100;
        $lance->save();
    
        // Act
        $exceptionMessage = '';
        try {
            $lance = new Lance();
            $lance->leilao_id = $leilao->id;
            $lance->usuario_id = $user->id;
            $lance->valor = 1050;
            $lance->save();
        } catch (\Exception $e) {
            $exceptionMessage = $e->getMessage();
        }
    
        // Assert
        $this->assertEquals('O valor do lance deve ser maior que o maior lance atual.', $exceptionMessage);
        $this->assertCount(1, Lance::all());
        $this->assertEquals(1100, Lance::first()->valor);
    }


    public function testUmMesmoUsuarioNaoPodeDarDoisLancesSeguidos()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->status = 'ABERTO';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();
    
        $user = new User();
        $user->name = 'Fulano';
        $user->email = 'fulano@email';
        $user->password = bcrypt('12345678');
        $user->save();
    
        $lance = new Lance();
        $lance->leilao_id = $leilao->id;
        $lance->usuario_id = $user->id;
        $lance->valor = 1100;
        $lance->save();
    
        // Act
        $exceptionMessage = '';
        try {
            $lance = new Lance();
            $lance->leilao_id = $leilao->id;
            $lance->usuario_id = $user->id;
            $lance->valor = 1200;
            $lance->save();
        } catch (\Exception $e) {
            $exceptionMessage = $e->getMessage();
        }
    
        // Assert
        $this->assertEquals('O usuário não pode dar dois lances seguidos.', $exceptionMessage);
        $this->assertCount(1, Lance::all());
        $this->assertEquals(1100, Lance::first()->valor);
    }

    public function testBuscarListaDeLancesPraUmDeterminadoLeilaoEmOrdemCrescenteDeValor()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->status = 'ABERTO';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();
    
        $user1 = new User();
        $user1->name = 'Fulano';
        $user1->email = 'fulano@email';
        $user1->password = bcrypt('12345678');
        $user1->save();
    
        $user2 = new User();
        $user2->name = 'Ciclano';
        $user2->email = 'ciclano@email';
        $user2->password = bcrypt('12345678');
        $user2->save();
    
        $user3 = new User();
        $user3->name = 'Beltrano';
        $user3->email = 'beltrano@email';
        $user3->password = bcrypt('12345678');
        $user3->save();
    
        $lance1 = new Lance();
        $lance1->leilao_id = $leilao->id;
        $lance1->usuario_id = $user1->id;
        $lance1->valor = 1100;
        $lance1->save();
    
        $lance2 = new Lance();
        $lance2->leilao_id = $leilao->id;
        $lance2->usuario_id = $user2->id;
        $lance2->valor = 1200;
        $lance2->save();
    
        $lance3 = new Lance();
        $lance3->leilao_id = $leilao->id;
        $lance3->usuario_id = $user3->id;
        $lance3->valor = 1300;
        $lance3->save();
    
        // Act
        $lances = Lance::where('leilao_id', $leilao->id)->orderBy('valor', 'asc')->get();
    
        // Assert
        $this->assertCount(3, $lances);
        $this->assertEquals(1100, $lances[0]->valor);
        $this->assertEquals(1200, $lances[1]->valor);
        $this->assertEquals(1300, $lances[2]->valor);
    }

    public function testBuscaMaiorEMenorLanceDeUmLeilao()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->status = 'ABERTO';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();
    
        $user1 = new User();
        $user1->name = 'Fulano';
        $user1->email = 'fulano@email';
        $user1->password = bcrypt('12345678');
        $user1->save();
    
        $user2 = new User();
        $user2->name = 'Ciclano';
        $user2->email = 'ciclano@email';
        $user2->password = bcrypt('12345678');
        $user2->save();
    
        $user3 = new User();
        $user3->name = 'Beltrano';
        $user3->email = 'beltrano@email';
        $user3->password = bcrypt('12345678');
        $user3->save();
    
        $lance1 = new Lance();
        $lance1->leilao_id = $leilao->id;
        $lance1->usuario_id = $user1->id;
        $lance1->valor = 1100;
        $lance1->save();
    
        $lance2 = new Lance();
        $lance2->leilao_id = $leilao->id;
        $lance2->usuario_id = $user2->id;
        $lance2->valor = 1200;
        $lance2->save();
    
        $lance3 = new Lance();
        $lance3->leilao_id = $leilao->id;
        $lance3->usuario_id = $user3->id;
        $lance3->valor = 1300;
        $lance3->save();
    
        // Act
        $maiorLance = Lance::where('leilao_id', $leilao->id)->max('valor');
        $menorLance = Lance::where('leilao_id', $leilao->id)->min('valor');

        // Assert
        $this->assertEquals(1300, $maiorLance);
        $this->assertEquals(1100, $menorLance);
    }

    public function testInserirLance()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();

        $user = new User();
        $user->name = 'Fulano';
        $user->email = 'fulano@email.com';
        $user->password = bcrypt('123456');
        $user->save();

        // Act
        $lance = new Lance();
        $lance->leilao_id = $leilao->id;
        $lance->usuario_id = $user->id;
        $lance->valor = 1000;
        $lance->save();

        // Assert
        $this->assertDatabaseHas('lances', [
            'leilao_id' => $leilao->id,
            'usuario_id' => $user->id,
            'valor' => 1000
        ]);
        $this->assertTrue($lance->id > 0);
        $this->assertCount(1, Lance::all());
        $this->assertEquals($leilao->id, $lance->leilao_id);
    }

    public function testAtualizarLance()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();

        $user = new User();
        $user->name = 'Fulano';
        $user->email = 'fulano@email.com';
        $user->password = bcrypt('123456');
        $user->save();

        // Act
        $lance = new Lance([
            'leilao_id' => $leilao->id,
            'usuario_id' => $user->id,
            'valor' => 1000
        ]);
        $lance->save();

        // Assert
        $this->assertCount(1, Lance::all());
        $this->assertDatabaseHas('lances', [
            'leilao_id' => $leilao->id,
            'usuario_id' => $user->id,
            'valor' => 1000
        ]);

        // Act (Update)
        $lance->valor = 2000;
        $lance->save();

        // Assert
        $this->assertCount(1, Lance::all());
        $this->assertDatabaseHas('lances', [
            'leilao_id' => $leilao->id,
            'usuario_id' => $user->id,
            'valor' => 2000
        ]);
    }

    public function testDeletarLance()
    {
        // Arrange
        $leilao = new Leilao();
        $leilao->nome = 'Leilão de um carro';
        $leilao->descricao = 'Leilão de um carro muito bonito';
        $leilao->data_inicio = '2024-01-01';
        $leilao->data_termino = '2024-01-02';
        $leilao->valor_inicial = 1000;
        $leilao->leilao_ganhador = null;
        $leilao->save();

        $user = new User();
        $user->name = 'Fulano';
        $user->email = 'fulano@email.com';
        $user->password = bcrypt('12345678');
        $user->save();

        // Act
        $lance = new Lance([
            'leilao_id' => $leilao->id,
            'usuario_id' => $user->id,
            'valor' => 1000
        ]);
        $lance->save();
        $lance->delete();

        // Assert
        $this->assertCount(0, Lance::all());
        $this->assertNull(Lance::find($lance->id));
    }

}