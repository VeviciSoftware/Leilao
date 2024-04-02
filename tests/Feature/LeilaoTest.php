<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Leilao;
use Tests\TestCase;

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

    public function test_index(): void
    {
        $response = $this->get('/api/leilao');

        $response->assertStatus(200);
        $response->assertJson([
            'leilao' => 'Leilão API',
            'versao' => '1.0.0'
        ]);
    }

    public function testeDeCriacaoDeLeilao() {
        $response = $this->post('/api/leilao', [
            'nome' => 'Leilão de um carro',
            'descricao' => 'Leilão de um carro usado',
            'valor_inicial' => 1000,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'ABERTO'
        ]);
    
        $response->assertStatus(201);
    }

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
}
