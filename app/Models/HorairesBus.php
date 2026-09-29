<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorairesBus extends Model
{
    use HasFactory;

    protected $table = 'horaires_bus';
    protected $fillable = ['bus_id', 'heure_depart', 'heure_arrivee', 'date_disponible', 'nb_places_totales', 'nb_places_restantes'];

    protected $casts = [
        'heure_depart' => 'datetime',
        'heure_arrivee' => 'datetime',
        'date_disponible' => 'date',
    ];

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
