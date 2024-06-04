<?php

namespace Tests\Unit;

use App\Models\Leilao;
use App\Services\Leilao\Encerrador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Mockery;


class ApiLeilaoControllerTest extends TestCase {
    use RefreshDatabase;
    public function tearDown(): void
    {
        Mockery::close();
    }

    public function test_get_leiloes(): void
    {
        $leilao = Leilao::factory(3)->create();

        $response = $this->get('/api/leilao');

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJsonStructure([
            '*' => [
                'nome',
                'descricao',
                'valor_inicial',
                'data_inicio',
                'data_termino',
                'status',
            ]
        ]);
    }

    public function test_get_single_leilao(): void
    {
        $leilao = Leilao::factory(1)->create()->first();
    
        $response = $this->get('/api/leilao/' . $leilao->id);
    
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'mensagem',
            'leilao' => [
                'nome',
                'descricao',
                'valor_inicial',
                'data_inicio',
                'data_termino',
                'status',
            ]
        ]);
    }

    public function test_create_leilao(): void
    {
        $leilao = Leilao::factory()->make()->toArray();

        $leilao['valor_inicial'] = (float) $leilao['valor_inicial'];
    
        $response = $this->post('/api/leilao', $leilao);

        //dd($response->getContent());
    
        $response->assertStatus(201);
    
        $response->assertJson(function (AssertableJson $json) use ($leilao) {
            $json->hasAll([
                'original.nome',
                'original.descricao',
                'original.valor_inicial',
                'original.data_inicio',
                'original.data_termino',
                'original.status'
            ]);

            $json->whereAll([
                'original.nome' => $leilao['nome'],
                'original.descricao' => $leilao['descricao'],
                'original.valor_inicial' => $leilao['valor_inicial'],
                'original.data_inicio' => $leilao['data_inicio'],
                'original.data_termino' => $leilao['data_termino'],
                'original.status' => 'INATIVO'
            ])->etc();
                
        });
    }

    // public function testFinalizarLeilao()
    // {
    //     // Cria um mock para o modelo Leilao
    //     $leilao = Mockery::mock(Leilao::class);
    //     $leilao->shouldReceive('getAttribute')
    //            ->with('finalizado')
    //            ->andReturn(false); // Simula um leilão 'ABERTO'
    //     $leilao->shouldReceive('setAttribute')
    //            ->with('finalizado', true)
    //            ->andReturn(true);
    //     $leilao->shouldReceive('save')->once();
    
    //     // Cria um mock para a classe Encerrador
    //     $encerrador = Mockery::mock(Encerrador::class, [$leilao]);
    //     $encerrador->shouldReceive('encerra')->once()
    //                ->andReturnUsing(function () use ($leilao) {
    //                    $leilao->finalizado = true;
    //                    $leilao->save();
    //                });
    
    //     // Chama o método encerra() do mock Encerrador
    //     $encerrador->encerra();
    
    //     // Verifica se o método save() do mock Leilao foi chamado
    //     $leilao->shouldHaveReceived('save');
    
    //     // Adiciona uma asserção do PHPUnit
    //     $this->assertTrue($leilao->finalizado);
    // }

}