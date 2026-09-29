<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['reservation_id', 'code_unique', 'prix', 'heure_depart', 'details_bus'];

    protected $casts = ['prix' => 'decimal:2'];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
