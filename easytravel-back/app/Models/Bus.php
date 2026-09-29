<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $table = 'bus';
    protected $fillable = ['numero_bus', 'capacite', 'chauffeur', 'type'];

    public function horaires()
    {
        return $this->hasMany(HorairesBus::class, 'bus_id');
    }
}
