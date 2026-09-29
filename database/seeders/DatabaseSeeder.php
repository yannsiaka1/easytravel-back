<?php

namespace Database\Seeders;

use App\Models\Connexion;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->seedProductionAgent();
            return;
        }

        $this->call([
            UtilisateurSeeder::class,
            ConnexionSeeder::class,
            BusSeeder::class,
            HorairesBusSeeder::class,
            VoyageSeeder::class,
            ReservationSeeder::class,
            TicketSeeder::class,
            PaiementSeeder::class,
            NotificationSeeder::class,
        ]);
    }

    /**
     * En production, aucun compte de démonstration n'est créé par défaut.
     * Si les deux variables sont présentes, elles servent à provisionner
     * le premier agent de manière idempotente lors du déploiement.
     */
    private function seedProductionAgent(): void
    {
        $email = env('EASYTRAVEL_AGENT_EMAIL');
        $password = env('EASYTRAVEL_AGENT_PASSWORD');

        if (! $email || ! $password) {
            return;
        }

        DB::transaction(function () use ($email, $password) {
            $connexion = Connexion::where('email', $email)->first();

            if ($connexion) {
                $connexion->update(['mot_de_passe' => Hash::make($password)]);
                $connexion->utilisateur?->update(['role' => 'agent']);
                return;
            }

            $agent = Utilisateur::create([
                'nom' => env('EASYTRAVEL_AGENT_NOM', 'Agent'),
                'prenom' => env('EASYTRAVEL_AGENT_PRENOM', 'EasyTravel'),
                'carte_identite' => env('EASYTRAVEL_AGENT_CNI', 'AGENT-EASYTRAVEL'),
                'telephone' => env('EASYTRAVEL_AGENT_TELEPHONE', '000000000'),
                'role' => 'agent',
            ]);

            Connexion::create([
                'utilisateur_id' => $agent->id,
                'email' => $email,
                'mot_de_passe' => Hash::make($password),
            ]);
        });
    }
}
