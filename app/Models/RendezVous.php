<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Bonne pratique d'importer les types de relations

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'echantillon_enquete_id',
        'utilisateur_id',
        'heure_rdv',        // Corrigé: remplace heure_debut et heure_fin
        'contact_rdv',      // Ajouté: nouveau champ
        'statut',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'heure_rdv' => 'datetime', // Utilisation de $casts pour la conversion en objet Carbon/DateTime
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'statut' => 'planifie', // Optionnel: pour définir la valeur par défaut au niveau du modèle
    ];

    // Relation avec EchantillonEnquete
    public function echantillonEnquete(): BelongsTo
    {
        // Assurez-vous que App\Models\EchantillonEnquete est le bon chemin si ce n'est pas dans le même namespace
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    // Relation avec User
    public function utilisateur(): BelongsTo
    {
        // Assurez-vous que App\Models\User est le bon chemin
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}