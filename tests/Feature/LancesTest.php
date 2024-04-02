<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Leilao;
use App\Models\User;
use App\Models\Lance;

class LancesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    //Lance criado com sucesso.
    public function testStoreSuccess()
    {
        $leilao = Leilao::factory()->create(['finalizado' => false]);
        $user = User::factory()->create();

        $response = $this->postJson('/api/lance/store', [
            'valor' => 100,
            'leilao_id' => $leilao->id,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'Lance realizado com sucesso']);
    }

    public function testStoreSuccessUsingMock(): void
    {
        $leilao = Leilao::factory()->create(['finalizado' => false]);
        $user = User::factory()->create();

        $this->mock(Leilao::class, function ($mock) use ($leilao) {
            $mock->shouldReceive('find')->once()->andReturn($leilao);
        });

        $response = $this->postJson('/api/lance/store', [
            'valor' => 100,
            'leilao_id' => $leilao->id,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200)
            ->assertJson(['mensagem' => 'Lance realizado com sucesso']);
    }

    //Leilão finalizado.
    public function testStoreFailLeilaoFinalizado()
    {
        $leilao = Leilao::factory()->create(['finalizado' => true]);
        $user = User::factory()->create();

        $response = $this->postJson('/api/lance/store', [
            'valor' => 100,
            'leilao_id' => $leilao->id,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['mensagem' => 'O leião já foi finalizado. Não é possível realizar lances.']);
    }

    public function testStoreFailLanceMenor()
    {
        $leilao = Leilao::factory()->create(['finalizado' => false]);
        $user = User::factory()->create();
        Lance::create(['valor' => 200, 'leilao_id' => $leilao->id, 'user_id' => $user->id]);

        $response = $this->postJson('/api/lance/store', [
            'valor' => 100,
            'leilao_id' => $leilao->id,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(400)
            ->assertJson(['mensagem' => 'Lance deve ser maior que o lance anterior']);
    }
}
