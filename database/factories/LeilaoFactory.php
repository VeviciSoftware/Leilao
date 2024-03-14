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
            'valor_inicial' => $this->faker->randomFloat(2, 1000, 10000),
            'data_inicio' => $this->faker->date(),
            'data_termino' => $this->faker->date(),
            'status' => $this->faker->randomElement(['ABERTO', 'FINALIZADO'])
        ];
    }
}