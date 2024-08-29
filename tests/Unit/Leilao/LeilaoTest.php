<?php

namespace Tests\Unit\Leilao;

use App\Http\Requests\LeilaoRequest;
use Tests\TestCase;
use App\Repositories\EloquentLeilaoRepository;
use Mockery;

class LeilaoTest extends TestCase {

    public function testAddLeilao()
    {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1000,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn((object) $leilaoData);
    
        $leilao = $repository->add($request);
    
        $this->assertEquals('Leilao de um carro', $leilao->nome);
        $this->assertEquals('Leilao de um carro usado', $leilao->descricao);
        $this->assertEquals(1000, $leilao->valor_inicial);
        $this->assertEquals('2021-10-01', $leilao->data_inicio);
        $this->assertEquals('2021-10-10', $leilao->data_termino);
        $this->assertEquals('INATIVO', $leilao->status);
    }

    public function testLeilaoDeveTerValorInicialMaiorQueZero()
    {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 0,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn(response()->json([
                'message' => 'O valor inicial do leilão deve ser maior que zero'
            ], 400));
    
        $response = $repository->add($request);
    
        $this->assertEquals('O valor inicial do leilão deve ser maior que zero', $response->getData(true)['message']);
        $this->assertEquals(400, $response->status());
    }

    public function testLeilaoNaoDeveTerValorInicialMenorQueZero() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => -1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn(response()->json([
                'message' => 'O valor inicial do leilão deve ser maior que zero'
            ], 400));
    
        $response = $repository->add($request);
    
        $this->assertEquals('O valor inicial do leilão deve ser maior que zero', $response->getData(true)['message']);
        $this->assertEquals(400, $response->status());
    }

    public function testLeilaoDeverTerValorInicialDiferenteDeZero() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn((object) $leilaoData);
    
        $leilao = $repository->add($request);
    
        $this->assertEquals('Leilao de um carro', $leilao->nome);
        $this->assertEquals('Leilao de um carro usado', $leilao->descricao);
        $this->assertEquals(1, $leilao->valor_inicial);
        $this->assertEquals('2021-10-01', $leilao->data_inicio);
        $this->assertEquals('2021-10-10', $leilao->data_termino);
        $this->assertEquals('INATIVO', $leilao->status);
    }

    public function testLeilaoDeveTerDataInicioValida() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn((object) $leilaoData);
    
        $leilao = $repository->add($request);
    
        $this->assertEquals('Leilao de um carro', $leilao->nome);
        $this->assertEquals('Leilao de um carro usado', $leilao->descricao);
        $this->assertEquals(1, $leilao->valor_inicial);
        $this->assertEquals('2021-10-01', $leilao->data_inicio);
        $this->assertEquals('2021-10-10', $leilao->data_termino);
        $this->assertEquals('INATIVO', $leilao->status);
    }
    
    public function testLeilaoDeveFalharComDataInicioInvalida()
    {
        // Cria um leilao com data de início inválida
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => 'data-invalida',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Valida o request
        $validator = \Validator::make($request->all(), $request->rules());
    
        // Verifica se a validação falhou
        $this->assertTrue($validator->fails());
        $this->assertContains('data_inicio', array_keys($validator->failed()));
    }

    public function testLeilaoDeveTerDataTerminoValida() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn((object) $leilaoData);
    
        $leilao = $repository->add($request);
    
        $this->assertEquals('Leilao de um carro', $leilao->nome);
        $this->assertEquals('Leilao de um carro usado', $leilao->descricao);
        $this->assertEquals(1, $leilao->valor_inicial);
        $this->assertEquals('2021-10-01', $leilao->data_inicio);
        $this->assertEquals('2021-10-10', $leilao->data_termino);
        $this->assertEquals('INATIVO', $leilao->status);
    }

    public function testLeilaoDeveFalharComDataTerminoInvalida()
    {
        // Cria um leilao com data de término inválida
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => 'data-invalida',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Valida o request
        $validator = \Validator::make($request->all(), $request->rules());
    
        // Verifica se a validação falhou
        $this->assertTrue($validator->fails());
        $this->assertContains('data_termino', array_keys($validator->failed()));
    }

    public function testLeilaoDeveTerDataTerminoMaiorQueDataInicio() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Cria um mock do EloquentLeilaoRepository
        $repository = Mockery::mock(EloquentLeilaoRepository::class);
        $repository->shouldReceive('add')
            ->once()
            ->with(Mockery::on(function ($arg) use ($request) {
                return $arg == $request;
            }))
            ->andReturn((object) $leilaoData);
    
        $leilao = $repository->add($request);
    
        $this->assertEquals('Leilao de um carro', $leilao->nome);
        $this->assertEquals('Leilao de um carro usado', $leilao->descricao);
        $this->assertEquals(1, $leilao->valor_inicial);
        $this->assertEquals('2021-10-01', $leilao->data_inicio);
        $this->assertEquals('2021-10-10', $leilao->data_termino);
        $this->assertEquals('INATIVO', $leilao->status);
    }

    // Nome não pode ser vazio ou nulo
    public function testLeilaoDeveTerNomePreenchido() {
        // Cria um leilao
        $leilaoData = [
            'nome' => '',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Valida o request
        $validator = \Validator::make($request->all(), $request->rules());
    
        // Verifica se a validação falhou
        $this->assertTrue($validator->fails());
        $this->assertContains('nome', array_keys($validator->failed()));
    }

    // Leilão não pode ter descrição vazia ou nula
    public function testLeilaoDeveTerDescricaoPreenchida() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => '',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INATIVO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Valida o request
        $validator = \Validator::make($request->all(), $request->rules());
    
        // Verifica se a validação falhou
        $this->assertTrue($validator->fails());
        $this->assertContains('descricao', array_keys($validator->failed()));
    }

    // Status do leilão deve ser válido
    public function testLeilaoDeveTerStatusValido() {
        // Cria um leilao
        $leilaoData = [
            'nome' => 'Leilao de um carro',
            'descricao' => 'Leilao de um carro usado',
            'valor_inicial' => 1,
            'data_inicio' => '2021-10-01',
            'data_termino' => '2021-10-10',
            'status' => 'INVALIDO'
        ];
    
        $request = new LeilaoRequest($leilaoData);
    
        // Valida o request
        $validator = \Validator::make($request->all(), $request->rules());
    
        // Verifica se a validação falhou
        $this->assertTrue($validator->fails());
        $this->assertContains('status', array_keys($validator->failed()));
    }


    
}
