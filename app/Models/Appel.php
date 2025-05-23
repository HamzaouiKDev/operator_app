<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appel extends Model
{
    protected $table = 'appels';

    protected $fillable = ['echantillon_enquete_id', 'utilisateur_id', 'heure_debut', 'heure_fin', 'statut', 'notes'];

    public function echantillonEnquete()
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}