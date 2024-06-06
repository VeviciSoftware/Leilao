<?php

namespace Tests\Feature\Feature;

use App\Models\Leilao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Lance;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiLanceControllerTest extends TestCase {

use RefreshDatabase;
    public function test_get_lances(): void
    {
        $user = User::factory()->create();

        $leilao = Leilao::factory()->create();

        // Cria 3 lances com o usuário e o leilão criados
        $lance = Lance::factory(3)->create(['usuario_id' => $user->id, 'leilao_id' => $leilao->id]);

        $response = $this->get('/api/lances');

        $response->assertStatus(200);

        $response->assertJsonCount(3);

        $response->assertJsonStructure([
            '*' => [
                'leilao_id',
                'valor',
                'usuario_id',
            ]
        ]);
    }

    public function test_get_single_lance(): void
    {
        $user = User::factory()->create();

        $leilao = Leilao::factory()->create();

        $lance = Lance::factory()->create(['usuario_id' => $user->id, 'leilao_id' => $leilao->id])->first();
    
        $response = $this->get('/api/lances/' . $lance->id);
    
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'mensagem',
            'lance' => [
                'leilao_id',
                'valor',
                'usuario_id',
            ]
        ]);
    }

    public function test_create_lance(): void
    {
        $user = User::factory()->create();
    
        // Cria um leilão com valor inicial de 50
        $leilao = Leilao::factory()->create(['valor_inicial' => 50]);
    
        $lanceData = [
            'leilao_id' => $leilao->id,
            'valor' => 100, // Valor do lance é maior que o valor inicial do leilão
            'usuario_id' => $user->id,
        ];
    
        $response = $this->post('/api/lances', $lanceData);
    
        $response->assertStatus(201);
    
        $response->assertJson([
            'leilao_id' => $leilao->id,
            'valor' => 100,
            'usuario_id' => $user->id,
        ]);
    }

    public function test_put_lance(): void
    {
        $user = User::factory()->create();
    
        $leilao = Leilao::factory()->create();
    
        $lance = Lance::factory()->create(['usuario_id' => $user->id, 'leilao_id' => $leilao->id]);
    
        $lanceData = [
            'leilao_id' => $leilao->id,
            'valor' => 100,
            'usuario_id' => $user->id,
        ];
    
        $response = $this->put('/api/lances/' . $lance->id, $lanceData);
    
        $response->assertStatus(200);
    
        $response->assertJson([
            'leilao_id' => $leilao->id,
            'valor' => 100,
            'usuario_id' => $user->id,
        ]);
    
        // Recupera o lance do banco de dados e verifica se todos os valores foram atualizados
        $lanceAtualizado = Lance::find($lance->id);
        $this->assertEquals($leilao->id, $lanceAtualizado->leilao_id);
        $this->assertEquals(100, $lanceAtualizado->valor);
        $this->assertEquals($user->id, $lanceAtualizado->usuario_id);
    }

    public function test_patch_lance(): void 
    {
        $user = User::factory()->create();
    
        $leilao = Leilao::factory()->create();
    
        $lance = Lance::factory()->create(['usuario_id' => $user->id, 'leilao_id' => $leilao->id]);
    
        $lanceData = [
            'valor' => 100,
        ];
    
        $response = $this->patch('/api/lances/' . $lance->id, $lanceData);
    
        $response->assertStatus(200);
    
        $response->assertJson([
            'valor' => 100,
        ]);
    
        // Recupera o lance do banco de dados e verifica se o valor foi atualizado
        $lanceAtualizado = Lance::find($lance->id);
        $this->assertEquals(100, $lanceAtualizado->valor);
    }

    public function test_delete_lance(): void
    {
        $user = User::factory()->create();
    
        $leilao = Leilao::factory()->create();
    
        $lance = Lance::factory()->create(['usuario_id' => $user->id, 'leilao_id' => $leilao->id]);
    
        $response = $this->delete('/api/lances/' . $lance->id);
    
        $response->assertStatus(204);
    
        // Verifica se o lance foi removido do banco de dados
        $this->assertNull(Lance::find($lance->id));
    }



    // public function test_get_single_lance(): void
    // {
    //     $lance = Lance::factory(1)->create()->first();
    
    //     $response = $this->get('/api/lance/' . $lance->id);
    
    //     $response->assertStatus(200);
    
    //     $response->assertJsonStructure([
    //         'leilao_id',
    //         'valor',
    //         'usuario_id',
    //     ]);
    // }

    // public function test_add_lance(): void
    // {
    //     $lanceData = [
    //         'leilao_id' => 1,
    //         'valor' => 100,
    //         'usuario_id' => 1,
    //     ];

    //     $response = $this->post('/api/lance', $lanceData);

    //     $response->assertStatus(201);

    //     $response->assertJson([
    //         'leilao_id' => 1,
    //         'valor' => 100,
    //         'usuario_id' => 1,
    //     ]);
    // }

    // public function test_put_lance(): void
    // {
    //     $lance = Lance::factory()->create();

    //     $lanceData = [
    //         'leilao_id' => 1,
    //         'valor' => 100,
    //         'usuario_id' => 1,
    //     ];

    //     $response = $this->put('/api/lance/' . $lance->id, $lanceData);

    //     $response->assertStatus(200);

    //     $response->assertJson([
    //         'leilao_id' => 1,
    //         'valor' => 100,
    //         'usuario_id' => 1,
    //     ]);
    // }

    // public function test_patch_lance(): void 
    // {
    //     $lance = Lance::factory()->create();

    //     $lanceData = [
    //         'valor' => 100,
    //     ];

    //     $response = $this->patch('/api/lance/' . $lance->id, $lanceData);

    //     $response->assertStatus(200);

    //     $response->assertJson([
    //         'valor' => 100,
    //     ]);
    // }

    // public function test_delete_lance(): void
    // {
    //     $lance = Lance::factory()->create();

    //     $response = $this->delete('/api/lance/' . $lance->id);

    //     $response->assertStatus(204);
    // }


}
