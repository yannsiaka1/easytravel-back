<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voyage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ville_depart',
        'ville_arrivee',
        'date_depart',
        'prix',
        'nombre_places',
        'agent_id',
        'classe',
    ];

    protected $casts = [
        'date_depart' => 'datetime',
        'prix' => 'decimal:2',
        'nombre_places' => 'integer',
    ];

    public function agent()
    {
        return $this->belongsTo(Utilisateur::class, 'agent_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
