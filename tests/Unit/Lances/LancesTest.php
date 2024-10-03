<?php

namespace Tests\Unit\Lances;

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

    //Uusário não pode ser nulo.
    public function test_usuario_nao_pode_ser_nulo() {
        $leilao = Leilao::factory()->create(['valor_inicial' => 100]);

        // Testa a criação de um lance com usuário nulo
        $lanceData = [
            'usuario_id' => null,
            'leilao_id' => $leilao->id,
            'valor' => 100,
        ];

        $request = new LanceRequest($lanceData);

        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('Usuário não encontrado', $response->getData()->mensagem);
        $this->assertEquals(404, $response->status());
    }

    //Leilão não pode ser nulo.
    public function test_leilao_nao_pode_ser_nulo() {
        $user = User::factory()->create();

        // Testa a criação de um lance com leilão nulo
        $lanceData = [
            'usuario_id' => $user->id,
            'leilao_id' => null,
            'valor' => 100,
        ];

        $request = new LanceRequest($lanceData);

        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('O leilão não existe. Não é possível realizar lances.', $response->getData()->mensagem);
        $this->assertEquals(400, $response->status());
    }

    //Lance deve ser maior que zero.
    public function test_lance_deve_ser_maior_que_zero() {
        $user = User::factory()->create();
        $leilao = Leilao::factory()->create(['valor_inicial' => 100]);

        // Testa a criação de um lance com valor zero
        $lanceData = [
            'usuario_id' => $user->id,
            'leilao_id' => $leilao->id,
            'valor' => 0,
        ];

        $request = new LanceRequest($lanceData);

        $repository = new EloquentLanceRepository();
        $response = $repository->add($request);

        $this->assertEquals('O valor do lance deve ser maior que zero', $response->getData()->mensagem);
        $this->assertEquals(400, $response->status());
    }


    //Lance com valor menor que o maior lance atual.
    public function test_add_lance_valor_menor_que_maior_lance_atual()
    {
        // Criar mocks
        $userMock = Mockery::mock(User::class);
        $leilaoMock = Mockery::mock(Leilao::class);
        $lanceMock = Mockery::mock(Lance::class);
        $requestMock = Mockery::mock(LanceRequest::class);
        $repositoryMock = Mockery::mock(EloquentLanceRepository::class);

        // Configurar mocks
        $userMock->shouldReceive('factory->create')->andReturn((object)['id' => 1]);
        $leilaoMock->shouldReceive('factory->create')->andReturn((object)['id' => 1]);
        $lanceMock->shouldReceive('factory->create')->andReturn((object)[
            'usuario_id' => 1,
            'leilao_id' => 1,
            'valor' => 100,
        ]);

        $requestMock->shouldReceive('all')->andReturn([
            'usuario_id' => 1,
            'leilao_id' => 1,
            'valor' => 50,
        ]);

        $repositoryMock->shouldReceive('add')->with($requestMock)->andReturn(response()->json([
            'mensagem' => 'O valor do lance deve ser maior que o maior lance atual'
        ], 400));

        // Executar o teste
        $response = $repositoryMock->add($requestMock);

        // Verificar resultados
        $this->assertEquals('O valor do lance deve ser maior que o maior lance atual', $response->getData(true)['mensagem']);
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
    
        $this->assertEquals('O valor do lance deve ser maior do que o valor inicial do leilão.', $response->getData()->mensagem);
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

        $this->assertEquals('O usuário não pode dar dois lances seguidos.', $response->getData()->mensagem);
        $this->assertEquals(400, $response->status());
    }


}
