<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Utilisateur extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = ['nom', 'prenom', 'carte_identite', 'telephone', 'role'];

    public function connexion()
    {
        return $this->hasOne(Connexion::class, 'utilisateur_id');
    }

    public function routeNotificationForMail(): ?string
    {
        return $this->connexion?->email;
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function voyages()
    {
        return $this->hasMany(Voyage::class, 'agent_id');
    }
}
