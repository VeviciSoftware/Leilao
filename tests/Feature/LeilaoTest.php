<?php

namespace Tests\Feature;

use App\Http\Requests\LeilaoRequest;
use Tests\TestCase;
use App\Repositories\EloquentLeilaoRepository;
use Mockery;

class LeilaoTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_health_check(): void
    {
        $response = $this->get('/api/health-check');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok'
        ]);
    }

    public function testAddLeilao()
    {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1000,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn((object) $leilaoData);
    
        $leilao = $repository->add($request);
    
        $this->assertEquals('Leilao de um carro', $leilao->nome);
        $this->assertEquals('Leilao de um carro usado', $leilao->descricao);
        $this->assertEquals(1000, $leilao->valor_inicial);
        $this->assertEquals('2021-10-01', $leilao->data_inicio);
        $this->assertEquals('2021-10-10', $leilao->data_termino);
        $this->assertEquals('INATIVO', $leilao->status);
    }


    // public function testeDeCriacaoDeLeilaoUsandoMocks() {
    //     $leilao = Leilao::factory()->make();
    //     $response = $this->post('/api/leilao', $leilao->toArray());
    
    //     $response->assertStatus(201);
    // }

    // public function testeDeExclusaoDeLeilao()    
    // {
    //     $leilao = Leilao::factory()->create();
    //     $response = $this->deleteJson('/api/leiloes/' . $leilao->id);
      
    //     $response->assertStatus(204); // If leilao is found and deleted
      
    //     // OR
      
    //     if ($response->getStatusCode() === 404) {
    //       // Handle the case where leilao is not found (e.g., assert error message)
    //     } else {
    //       $this->assertDatabaseMissing('leiloes', ['id' => $leilao->id]);
    //     }
    // }

    // public function testeDeMudancaDeStatusDoLeilaoParaExpirado() {
    //     // Cria um leilão com uma data de término no passado
    //     $leilao = Leilao::create([
    //         'nome' => 'Leilao Teste',
    //         'descricao' => 'Descricao Teste',
    //         'valor_inicial' => 100,
    //         'data_inicio' => Carbon::now()->subDays(2),
    //         'data_termino' => Carbon::now()->subDay(),
    //         'status' => 'ABERTO',
    //     ]);

    //     // Executa o comando ExpireLeiloes
    //     $this->artisan('leiloes:expire');

    //     // Recarrega o leilão do banco de dados
    //     $leilao = $leilao->fresh();

    //     // Verifica se o status do leilão foi alterado para 'EXPIRADO'
    //     $this->assertEquals('EXPIRADO', $leilao->status);
    // }
    
}
