<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connexion extends Model
{
    use HasFactory;

    protected $table = 'connexions';

    protected $fillable = ['utilisateur_id', 'email', 'mot_de_passe'];

    protected $hidden = ['mot_de_passe'];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
