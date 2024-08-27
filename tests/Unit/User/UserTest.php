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
        $user = new User();
        $user->name = 'Jo';
        $this->assertFalse($user->isValid());
    }

    public function testNomeDeveTerNoMaximo255Caracteres()
    {
        $user = new User();
        $user->name = str_repeat('a', 256);
        $this->assertFalse($user->isValid());
    }

    public function testEmailDeveSerValido()
    {
        $user = new User();
        $user->email = 'email_invalidoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo';
        $this->assertFalse($user->isValid());
    }

    public function testSenhaDeveTerNoMinimo8Caracteres()
    {
        $user = new User();
        $user->password = '1234567';
        $this->assertFalse($user->isValid());
    }

    public function testSenhaDeveTerNoMaximo16Caracteres()
    {
        $user = new User();
        $user->password = '12345678901234567';
        $this->assertFalse($user->isValid());
    }

    public function testUsuarioDeveSerInvalido()
    {
        $user = new User();
        $user->name = 'Jo';
        $user->email = 'email_invalidooooooooooooooooooooooooooooooooooooooooo';

        $this->assertFalse($user->isValid());
    }

}
