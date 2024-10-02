<?php

namespace Tests\Unit;

use App\Models\Leilao;
use App\Models\User;
use App\Services\Leilao\Encerrador;
use App\Http\Controllers\Api\ApiLeilaoController;
use App\Repositories\ILeilaoRepository;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Mockery;


class ApiLeilaoControllerTest extends TestCase
{
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
    
        // Ajustar as datas para garantir que a data de término seja maior que a data de início
        $leilao['data_inicio'] = now()->format('Y-m-d H:i:s');
        $leilao['data_termino'] = now()->addDay()->format('Y-m-d H:i:s');
    
        $response = $this->post('/api/leilao', $leilao);
    
        $response->assertStatus(201);

    }

    public function test_put_leilao(): void
    {
        $leilaoCriado = Leilao::factory(1)->createOne();

        $leilao = [
            'nome' => 'Leilão de um Leksus',
            'descricao' => 'Leksus é um cara bacana.',
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

    // public function test_patch_leilao(): void
    // {
    //     $leilaoCriado = Leilao::factory(1)->createOne();

    //     $leilao = [
    //         'nome' => 'Leilão de um Leksus',
    //     ];

    //     $response = $this->patchJson('/api/leilao/' . $leilaoCriado->id, $leilao);

    //     $response->assertStatus(200);

    //     $response->assertJson(function (AssertableJson $json) use ($leilao) {
    //         $json->has('mensagem')
    //             ->has('leilao', function ($json) use ($leilao) {
    //                 $json->whereAll([
    //                     'nome' => $leilao['nome'],
    //                 ])->etc();
    //             });
    //     });
    // }


    public function destroy($id)
    {
        $leilao = Leilao::findOrFail($id);
        $leilao->delete();

        return response()->json(['mensagem' => 'Leilão deletado com sucesso'], 204);
    }


    public function test_get_lista_de_lances_em_um_leilao()
    {
        $leilao = Leilao::factory(1)->create()->first();
        $user = User::factory()->create();
        $lances = $leilao->lances()->createMany([
            ['valor' => 1000, 'usuario_id' => $user->id],
            ['valor' => 2000, 'usuario_id' => $user->id],
            ['valor' => 3000, 'usuario_id' => $user->id],
        ]);

        $response = $this->get('/api/leilao/' . $leilao->id . '/lances');

        $response->assertStatus(200);

        // Verifica se o número total de lances é 3
        $response->assertJsonCount(3, 'lances');

        $response->assertJsonStructure([
            'leilao' => [
                'nome',
                'descricao',
                'valor_inicial',
                'data_inicio',
                'data_termino',
                'status'
            ],
            'lances' => [
                '*' => [
                    'valor',
                    'created_at',
                    'participante' => [
                        'id',
                        'email',
                        'name'
                    ]
                ]
            ]
        ]);
    }


    // public function testEncerrarLeilao()
    // {
    //     // Cria um mock para o modelo Leilao
    //     $leilao = $this->createMock(Leilao::class);
    //     $leilao->method('save')
    //         ->willReturn(true);

    //     // Cria um mock para a interface ILeilaoRepository
    //     $repository = $this->createMock(ILeilaoRepository::class);
    //     $repository->method('getLeilaoById')
    //         ->willReturn($leilao);

    //     // Cria um mock para a classe Encerrador
    //     $encerrador = $this->createMock(Encerrador::class);
    //     $encerrador->method('encerra')
    //         ->willReturn(null);

    //     // Cria um mock para a classe Request
    //     $request = $this->createMock(Request::class);

    //     // Cria uma instância do controlador com os mocks
    //     $controller = new ApiLeilaoController($repository, $leilaoService);

    //     // Chama o método encerrarLeilao
    //     $response = $controller->encerraLeiloes($request);

    //     // Verifica se a resposta é a esperada
    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals(['mensagem' => 'Leilões expirados finalizados com sucesso.'], $response->getData(true));
    // }

}

