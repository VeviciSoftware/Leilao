<?php

namespace Tests\Integration\Database;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    private static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password TEXT);');
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    /**
     * @dataProvider users
    */
    public function testCreateUser($name, $email, $password)
    {
        $stmt = self::$pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $password]);

        $stmt = self::$pdo->query('SELECT * FROM users WHERE email = \'' . $email . '\'');
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($user);
        $this->assertEquals($name, $user['name']);
        $this->assertEquals($email, $user['email']);
        $this->assertEquals($password, $user['password']);
    }

    /**
     * @dataProvider users
     */
    public function testEditUser($name, $email, $password)
    {
        $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);

        $newName = 'Novo Nome';
        $newEmail = 'novoemail@email.com';
        $newPassword = 'novasenha';

        $user->update(['name' => $newName, 'email' => $newEmail, 'password' => $newPassword]);

        $this->assertDatabaseHas('users', [
            'name' => $newName,
            'email' => $newEmail,
            'password' => $newPassword,
        ]);
    }

    /**
     * @dataProvider users
    */
    public function testGetUser($name, $email, $password)
    {
        $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);

        $retrievedUser = User::where('email', $email)->first();

        $this->assertNotNull($retrievedUser);
        $this->assertEquals($name, $retrievedUser->name);
        $this->assertEquals($email, $retrievedUser->email);
        $this->assertEquals($password, $retrievedUser->password);
    }

    /**
     * @dataProvider users
    */
    public function testListUsers() 
    {
        $users = [
            ['name' => 'João', 'email' => 'joao@gmail.com', 'password' => '12345678'],
            ['name' => 'Maria', 'email' => 'maria@email.com', 'password' => '12345678'],
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