<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appel extends Model
{
    use HasFactory;

    protected $fillable = [
        'echantillon_enquete_id',
        'utilisateur_id',
        'heure_debut',
        'heure_fin',
        'statut',
        'notes',
        'telephone_utilise_id',         // ✅ Nouveau
        'numero_compose',               // ✅ Nouveau
        'statut_numero_au_moment_appel', // ✅ Nouveau
    ];

    protected $casts = [
        'heure_debut' => 'datetime',
        'heure_fin' => 'datetime',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function echantillonEnquete()
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    // ✅ Nouvelle relation optionnelle
    public function telephoneUtilise()
    {
        return $this->belongsTo(TelephoneEntreprise::class, 'telephone_utilise_id');
    }
}