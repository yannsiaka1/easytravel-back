<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['utilisateur_id', 'type', 'contenu', 'lu', 'date_envoi'];

    protected $casts = ['lu' => 'boolean', 'date_envoi' => 'datetime'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
