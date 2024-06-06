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
    //Lance com valor menor que o maior lance atual.
    public function test_add_lance_valor_menor_que_maior_lance_atual()
    {
        $user = User::factory()->create();
        $leilao = Leilao::factory()->create();

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

        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('O valor do lance deve ser maior que o maior lance atual', $response->getData()->mensagem);
        $this->assertEquals(400, $response->status());
    }

    public function test_add_lance_menor_que_valor_inicial_leilao()
    {
        $user = User::factory()->create();
        $leilao = Leilao::factory()->create(['valor_inicial' => 100]);
    
        // Testa a criação de um lance com valor menor que o valor inicial do leilão
        $lanceData = [
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 50,
        ];
    
        $request = new LanceRequest($lanceData);
    
        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);
    
        $this->assertEquals('O valor do lance deve ser maior ou igual ao valor inicial do leilão', $response->getData()->mensagem);
        $this->assertEquals(400, $response->status());
    }

    public function test_usuario_nao_pode_dar_dois_lances_seguidos() 
    {
        $user = User::factory()->create();
        $leilao = Leilao::factory()->create(['valor_inicial' => 100]);

        $lance = Lance::factory()->create([
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 120,
        ]);

        $lanceData = [
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 150,
        ];

        $request = new LanceRequest($lanceData);

        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('O usuário não pode dar dois lances seguidos', $response->getData()->mensagem);
        $this->assertEquals(400, $response->status());
    }


}
