<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'echantillon_enquete_id',
        'utilisateur_id',
        'heure_debut',
        'heure_fin',
        'statut',
        'notes',
    ];

    protected $dates = [
        'heure_debut',
        'heure_fin',
        'created_at',
        'updated_at',
    ];
     

    // Relation avec EchantillonEnquete
    public function echantillonEnquete()
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    // Relation avec User
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}
