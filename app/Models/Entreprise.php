<?php

namespace App\Models;

use App\Models\Gouvernorat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entreprise extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés massivement.
     * C'est une protection de sécurité obligatoire dans Laravel.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code_national',
        'nom_entreprise',
        'libelle_activite',
        'gouvernorat_id', // <-- Important : on utilise la clé étrangère
        'numero_rue',
        'nom_rue',
        'ville',
        'statut',
        'adresse_cnss',
        'localite_cnss',
    ];

    /**
     * Définit la relation : Une entreprise APPARTIENT À un gouvernorat.
     */
    public function gouvernorat(): BelongsTo
    {
        // Laravel va automatiquement chercher la colonne `gouvernorat_id` dans cette table.
        return $this->belongsTo(Gouvernorat::class);
    }

    /**
     * Définit la relation : Une entreprise A PLUSIEURS numéros de téléphone.
     */
    public function telephones(): HasMany
    {
        return $this->hasMany(TelephoneEntreprise::class, 'entreprise_id');
    }

    /**
     * Définit la relation : Une entreprise A PLUSIEURS contacts.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(ContactEntreprise::class, 'entreprise_id');
    }

    /**
     * Définit la relation : Une entreprise A PLUSIEURS emails.
     */
    public function emails(): HasMany
    {
        return $this->hasMany(EmailEntreprise::class, 'entreprise_id');
    }

    /**
     * Définit la relation : Une entreprise PEUT ÊTRE DANS plusieurs échantillons d'enquête.
     */
    public function echantillons(): HasMany
    {
        return $this->hasMany(EchantillonEnquete::class, 'entreprise_id');
    }
}