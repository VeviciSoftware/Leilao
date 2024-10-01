<?php

namespace Tests\Integration\Database;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider users
    */
    public function testCreateUser($name, $email, $password)
    {
        $user = User::create(['name' => $name, 'email' => $email, 'password' => bcrypt($password)]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'email' => $email,
        ]);
    }

    /**
     * @dataProvider users
     */
    public function testEditUser($name, $email, $password)
    {
        $user = User::create(['name' => $name, 'email' => $email, 'password' => bcrypt($password)]);

        $newName = 'Novo Nome';
        $newEmail = 'novoemail@email.com';
        $newPassword = 'novasenha';

        $user->update(['name' => $newName, 'email' => $newEmail, 'password' => bcrypt($newPassword)]);

        $this->assertDatabaseHas('users', [
            'name' => $newName,
            'email' => $newEmail,
        ]);
    }

    /**
     * @dataProvider users
    */
    public function testGetUser($name, $email, $password)
    {
        $user = User::create(['name' => $name, 'email' => $email, 'password' => bcrypt($password)]);

        $retrievedUser = User::where('email', $email)->first();

        $this->assertNotNull($retrievedUser);
        $this->assertEquals($name, $retrievedUser->name);
        $this->assertEquals($email, $retrievedUser->email);
    }

    /**
     * @dataProvider users
    */
    public function testListUsers() 
    {
        $users = [
            ['name' => 'João', 'email' => 'joao@gmail.com', 'password' => bcrypt('12345678')],
            ['name' => 'Maria', 'email' => 'maria@email.com', 'password' => bcrypt('12345678')],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $retrievedUsers = User::all();

        $this->assertCount(2, $retrievedUsers);
    }

    public static function users(): array
    {
        return [
            ['name' => 'João', 'email' => 'joao@email.com', 'password' => '12345678'],
            ['name' => 'Maria', 'email' => 'maria@email.com', 'password' => '12345678'],
        ];
    }
}