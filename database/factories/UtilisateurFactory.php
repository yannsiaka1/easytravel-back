<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class UtilisateurFactory extends Factory
{
    protected $model = Utilisateur::class;

    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'carte_identite' => fake()->unique()->bothify('CI-########'),
            'telephone' => fake()->numerify('77#######'),
            'role' => 'client',
        ];
    }

    public function agent(): static
    {
        return $this->state(fn () => ['role' => 'agent']);
    }
}
