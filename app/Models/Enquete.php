<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquete extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'description',
        'statut',
        'titre_mail', // CORRIGÉ : Utilisation du bon nom de colonne
        'corps_mail',  // CORRIGÉ : Utilisation du bon nom de colonne
        'piece_jointe_path',
        'date_debut',
        'date_fin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_debut' => 'date', // CORRIGÉ : 'date' au lieu de 'datetime'
        'date_fin' => 'date',   // CORRIGÉ : 'date' au lieu de 'datetime'
    ];

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'enquete_id');
    }
}
