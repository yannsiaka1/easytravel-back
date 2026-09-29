<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use App\Models\Voyage;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoyageFactory extends Factory
{
    protected $model = Voyage::class;

    public function definition(): array
    {
        return [
            'ville_depart' => fake()->city(),
            'ville_arrivee' => fake()->city(),
            'date_depart' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'prix' => fake()->numberBetween(3000, 15000),
            'nombre_places' => fake()->numberBetween(10, 50),
            'classe' => fake()->randomElement(['classique', 'vip']),
            'agent_id' => Utilisateur::factory()->agent(),
        ];
    }
}
