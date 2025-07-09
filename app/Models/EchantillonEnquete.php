<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EchantillonEnquete extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'echantillons_enquetes';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'enquete_id',
        'statut',
        'priorite',
        'utilisateur_id',
        'date_attribution',
        'date_mise_a_jour',
        'date_liberation',
        'date_traitement',
        'commentaire',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_attribution' => 'datetime',
        'date_mise_a_jour' => 'datetime',
        'date_liberation'  => 'datetime',
        'date_traitement'  => 'datetime',
    ];

    /**
     * Obtenir le format de date à utiliser pour les requêtes de base de données.
     * Correction finale : on utilise le format ISO 8601 complet (avec "T")
     * pour garantir une interprétation correcte par SQL Server,
     * indépendamment de ses paramètres régionaux.
     *
     * @return string
     */
    public function getDateFormat()
    {
        return 'Y-m-d\TH:i:s.v';
    }

    // Relations

    /**
     * Obtenir l'entreprise associée à l'échantillon.
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Obtenir l'enquête associée à l'échantillon.
     */
    public function enquete(): BelongsTo
    {
        return $this->belongsTo(Enquete::class, 'enquete_id');
    }

    /**
     * Obtenir l'utilisateur assigné à l'échantillon.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
    
    /**
     * Obtenir l'historique des changements de statut pour l'échantillon.
     */
    public function statutHistories(): HasMany
    {
        // latest() trie les résultats par `created_at` (par défaut) en ordre décroissant.
        return $this->hasMany(EchantillonStatutHistory::class, 'echantillon_enquete_id')->latest();
    }
    public function suivis()
    {
        // Assurez-vous que 'echantillon_enquete_id' est bien la clé étrangère dans votre table 'suivis'
        return $this->hasMany(Suivi::class, 'echantillon_enquete_id');
    }


    public function appels()
{
    return $this->hasMany(Appel::class, 'echantillon_enquete_id');
}
}
