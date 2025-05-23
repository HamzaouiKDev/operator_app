<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class RendezVous extends Model
{
    protected $table = 'rendez_vous';

    protected $fillable = [
        'echantillon_enquete_id',
        'utilisateur_id',
        'heure_debut',
        'heure_fin',
        'statut',
        'notes',
    ];

    protected $casts = [
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
    ];

    public function echantillonEnquete()
    {
        return $this->belongsTo(EchantillonEnquete::class);
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class);
    }
}