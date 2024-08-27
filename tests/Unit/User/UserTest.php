<?php

namespace Tests\Unit\User;

use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    public function testNomeDeveTerNoMinimo3Caracteres()
    {
        $user = Mockery::mock(User::class);
        $user->name = 'Jo';
        $this->assertFalse($user->isValid());
    }

    public function testNomeDeveTerNoMaximo255Caracteres()
    {
        $user = Mockery::mock(User::class);
        $user->name = str_repeat('a', 256);
        $this->assertFalse($user->isValid());
    }

    public function testEmailDeveSerValido()
    {
        $user = Mockery::mock(User::class);
        $user->email = 'email_invalido';
        $this->assertFalse($user->isValid());
    }

    public function testSenhaDeveTerNoMinimo8Caracteres()
    {
        $user = Mockery::mock(User::class);
        $user->password = '1234567';
        $this->assertFalse($user->isValid());
    }

    public function testSenhaDeveTerNoMaximo16Caracteres()
    {
        $user = Mockery::mock(User::class);
        $user->password = str_repeat('a', 17);
        $this->assertFalse($user->isValid());
    }

    public function testUsuarioDeveSerInvalido()
    {
        $user = Mockery::mock(User::class);
        $user->name = 'Jo';
        $user->email = 'email_invalido';
        $this->assertFalse($user->isValid());
    }

}
