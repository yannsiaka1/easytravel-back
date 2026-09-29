<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        Utilisateur::insert([
            [
                'nom' => 'Fall',
                'prenom' => 'Amadou',
                'carte_identite' => 'ET-CLIENT-001',
                'telephone' => '770000000',
                'role' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nom' => 'Diop',
                'prenom' => 'Sokhna',
                'carte_identite' => 'ET-AGENT-001',
                'telephone' => '781234567',
                'role' => 'agent',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
