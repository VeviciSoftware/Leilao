<?php

namespace Database\Factories;

use App\Models\Lance;
use Illuminate\Database\Eloquent\Factories\Factory;

class LanceFactory extends Factory {
    protected $model = Lance::class;

    public function definition()
    {
        return [
            'valor' => $this->faker->randomFloat(2, 1000, 10000),
            'leilao_id' => $this->faker->numberBetween(1, 10)
        ];
    }
}