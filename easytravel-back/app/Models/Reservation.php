<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'utilisateur_id',
        'voyage_id',
        'date_voyage',
        'ville_depart',
        'destination',
        'classe',
        'nb_places',
        'statut_paiement',
        'date_reservation',
    ];

    protected $casts = [
        'date_voyage' => 'date',
        'date_reservation' => 'datetime',
        'nb_places' => 'integer',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function voyage()
    {
        return $this->belongsTo(Voyage::class);
    }

    public function paiement()
    {
        return $this->hasOne(Paiement::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }
}
