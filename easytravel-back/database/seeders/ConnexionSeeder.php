<?php

namespace Database\Seeders;

use App\Models\Connexion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ConnexionSeeder extends Seeder
{
    public function run(): void
    {
        Connexion::insert([
            [
                'utilisateur_id' => 1,
                'email' => 'client@easytravel.test',
                'mot_de_passe' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'utilisateur_id' => 2,
                'email' => 'agent@easytravel.test',
                'mot_de_passe' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
