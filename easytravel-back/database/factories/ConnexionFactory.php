<?php

namespace Database\Factories;

use App\Models\Connexion;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class ConnexionFactory extends Factory
{
    protected $model = Connexion::class;

    public function definition(): array
    {
        return [
            'utilisateur_id' => Utilisateur::factory(),
            'email' => fake()->unique()->safeEmail(),
            'mot_de_passe' => Hash::make('password123'),
        ];
    }
}
