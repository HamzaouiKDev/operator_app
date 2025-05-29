<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Suivi extends Model
{
    use HasFactory;

   protected $fillable = [
    'echantillon_enquete_id',
    'utilisateur_id',
    'date_suivi',
    'commentaire',
    'resultat',
    'cause_suivi' // Ajoutez cette ligne
];
    public function echantillonEnquete()
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}