<?php

namespace Tests\Feature;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Leilao;
use App\Models\User;
use App\Models\Lance;
use App\Http\Requests\LanceRequest;
use App\Repositories\EloquentLanceRepository;
use Carbon\Carbon;
use Mockery;

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
    //use RefreshDatabase;

    // public function testAddLance()
    // {
    //     // Cria um usuário e um leilão
    //     $user = Mockery::mock(User::class);
    //     $user->shouldReceive('getAttribute')
    //         ->with('id')
    //         ->andReturn(1);
    
    //     $leilao = Mockery::mock(Leilao::class);
    //     $leilao->shouldReceive('getAttribute')
    //         ->with('id')
    //         ->andReturn(1);
    
    //     // Testa a criação de um lance
    //     $lanceData = [
    //         'usuario_id' => $user->id,
    //         'leilao_id' => $leilao->id,
    //         'valor' => 100,
    //     ];
    
    //     $request = Mockery::mock(LanceRequest::class);
    //     $request->shouldReceive('all')
    //         ->once()
    //         ->andReturn($lanceData);
    
    //     $lance = Mockery::mock(Lance::class);
    //     $lance->shouldReceive('getAttribute')
    //         ->andReturnUsing(function ($attribute) use ($lanceData) {
    //             return $lanceData[$attribute];
    //         });
    
    //     $repository = Mockery::mock(EloquentLanceRepository::class);
    //     $repository->shouldReceive('add')
    //         ->once()
    //         ->with(Mockery::on(function ($arg) use ($request) {
    //             return $arg == $request;
    //         }))
    //         ->andReturn($lance);
    
    //     $response = $repository->add($request);
    
    //     $this->assertInstanceOf(Lance::class, $response);
    //     $this->assertEquals($user->id, $response->usuario_id);
    //     $this->assertEquals($leilao->id, $response->leilao_id);
    //     $this->assertEquals(100, $response->valor);
    // }

    //Lance com valor menor que o maior lance atual.
    public function testAddLanceValorMenorQueMaiorLanceAtual()
    {
        // Cria um usuário e um leilão
        $user = User::factory()->create();
        $leilao = Leilao::factory()->create();

        // Cria um lance
        $lance = Lance::factory()->create([
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 100,
        ]);

        // Testa a criação de um lance com valor menor que o maior lance atual
        $lanceData = [
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 50,
        ];

        $request = new LanceRequest($lanceData);

        $repository = new \App\Repositories\EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('O valor do lance deve ser maior que o maior lance atual', $response->getData()->mensagem);
    }

}
