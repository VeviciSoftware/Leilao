<?php

namespace Database\Factories;

use App\Models\Leilao;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeilaoFactory extends Factory
{
    protected $model = Leilao::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->sentence(3),
            'descricao' => $this->faker->paragraph(3),
        ];
    }
}