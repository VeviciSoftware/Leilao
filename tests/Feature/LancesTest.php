<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Leilao;
use App\Models\User;
use App\Models\Lance;
use App\Http\Requests\LanceRequest;

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
    use RefreshDatabase;

    public function testAddLance()
    {
        // Cria um usuário e um leilão
        $user = User::factory()->create();
        $leilao = Leilao::factory()->create();

        // Testa a criação de um lance
        $lanceData = [
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 100,
        ];

        $request = new LanceRequest($lanceData);

        $repository = new \App\Repositories\EloquentLanceRepository();
        $lance = $repository->add($request);

        $this->assertInstanceOf(Lance::class, $lance);
        $this->assertEquals($user->id, $lance->usuario_id);
        $this->assertEquals($leilao->id, $lance->leilao_id);
        $this->assertEquals(100, $lance->valor);
    }

}
