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

        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('O valor do lance deve ser maior que o maior lance atual', $response->getData()->mensagem);
    }

    public function testAddLanceMenorQueValorInicialLeilao()
    {
        // Cria um usuário e um leilão com um valor inicial de 100
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
    }


}
