<?php

namespace Tests\Unit;

use App\Models\Leilao;
use App\Services\Leilao\Encerrador;
use App\Http\Controllers\Api\ApiLeilaoController;
use App\Repositories\ILeilaoRepository;
use Illuminate\Http\Request;
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

    public function test_put_leilao(): void
    {
        $leilaoCriado = Leilao::factory(1)->createOne();

        $leilao = [
            'nome' => 'Leilão de um Leksus',
            'descricao' => 'Leksus é um cara bacana, ele gosta de sentar na cana, do Murilo',
            'valor_inicial' => 1000,
            'data_inicio' => '2021-10-10',
            'data_termino' => '2021-10-20',
            'status' => 'ABERTO'
        ];

        $response = $this->putJson('/api/leilao/' . $leilaoCriado->id, $leilao);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($leilao) {
            $json->has('mensagem')
                 ->has('leilao', function ($json) use ($leilao) {
                     $json->whereAll([
                        'nome' => $leilao['nome'],
                        'descricao' => $leilao['descricao'],
                        'valor_inicial' => $leilao['valor_inicial'],
                        'data_inicio' => $leilao['data_inicio'],
                        'data_termino' => $leilao['data_termino'],
                        'status' => $leilao['status']
                     ])->etc();
                 });
        });
        
    }

    public function test_patch_leilao(): void 
    {
        $leilaoCriado = Leilao::factory(1)->createOne();

        $leilao = [
            'nome' => 'Leilão de um Leksus',
        ];

        $response = $this->patchJson('/api/leilao/' . $leilaoCriado->id, $leilao);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($leilao) {
            $json->has('mensagem')
                 ->has('leilao', function ($json) use ($leilao) {
                     $json->whereAll([
                        'nome' => $leilao['nome'],
                     ])->etc();
                 });
        });
    }


    public function destroy($id)
    {
        $leilao = Leilao::findOrFail($id);
        $leilao->delete();
    
        return response()->json(['mensagem' => 'Leilão deletado com sucesso'], 204);
    }
    

    public function testEncerrarLeilao()
    {
        // Cria um mock para o modelo Leilao
        $leilao = $this->createMock(Leilao::class);
        $leilao->method('save')
               ->willReturn(true);
    
        // Cria um mock para a interface ILeilaoRepository
        $repository = $this->createMock(ILeilaoRepository::class);
        $repository->method('getLeilaoById')
                   ->willReturn($leilao);
    
        // Cria um mock para a classe Encerrador
        $encerrador = $this->createMock(Encerrador::class);
        $encerrador->method('encerra')
                   ->willReturn(null);
    
        // Cria um mock para a classe Request
        $request = $this->createMock(Request::class);
    
        // Cria uma instância do controlador com os mocks
        $controller = new ApiLeilaoController($repository);
    
        // Chama o método encerrarLeilao
        $response = $controller->encerrarLeilao($request, 1);
    
        // Verifica se a resposta é a esperada
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['mensagem' => 'Leilão finalizado com sucesso'], $response->getData(true));
    }

}

