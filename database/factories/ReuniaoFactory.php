<?php

namespace Database\Factories;

use App\Models\Reuniao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reuniao>
 */
class ReuniaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(3),
            'descricao' => $this->faker->paragraph(),
            'data' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'horario' => $this->faker->time('H:i'),
        ];
    }
}
