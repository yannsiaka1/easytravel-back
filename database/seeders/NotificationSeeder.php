<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Notification::insert([
            [
                'utilisateur_id' => 1,
                'type'           => 'info',
                'contenu'        => 'Votre voyage est confirmé.',
                'lu'             => 0,
                'date_envoi'     => Carbon::now(),
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]
        ]);
    }
}
