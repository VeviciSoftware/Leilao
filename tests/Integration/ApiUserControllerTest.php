<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testDeveMostrarTodosOsUsuarios(): void
    {
        User::factory()->count(3)->create();

        $response = $this->get('/api/users');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function testDeveCriarUmUsuario(): void
    {
        $response = $this->post('/api/users', [
            'name' => 'Joao',
            'email' => 'joao@gmail.com',
            'password' => '12345678',
        ]);
    
        $response->assertStatus(201);
        $response->assertJson([
            'mensagem' => 'Usuário criado com sucesso!',
            'user' => [
                'name' => 'Joao',
                'email' => 'joao@gmail.com',
            ],
        ]);
    
        $this->assertDatabaseHas('users', [
            'email' => 'joao@gmail.com',
        ]);
    }

    public function testDeveAtualizarUmUsuario(): void
    {
        $user = User::factory()->create();
    
        $response = $this->put("/api/users/{$user->id}", [
            'name' => 'Joao Atualizado',
            'email' => 'joao_atualizado@gmail.com',
            'password' => '12345678910',
        ]);
    
        $response->assertStatus(200);
        $response->assertJson([
            'mensagem' => 'Usuário atualizado com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => 'Joao Atualizado',
                'email' => 'joao_atualizado@gmail.com',
            ],
        ]);
    
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Joao Atualizado',
            'email' => 'joao_atualizado@gmail.com',
        ]);
    }

    public function testDeveExcluirUmUsuario(): void
    {
        $user = User::factory()->create();

        $response = $this->delete("/api/users/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}