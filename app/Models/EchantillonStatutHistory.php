<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EchantillonStatutHistory extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'echantillon_statut_histories';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'echantillon_enquete_id',
        'user_id',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
    ];

    /**
     * Spécifier le format de date pour la compatibilité avec SQL Server,
     * pour correspondre à la configuration du modèle EchantillonEnquete.
     *
     * @var string
     */
    protected $dateFormat = 'Ymd H:i:s';
    
    /**
     * On garde le casting pour les clés étrangères pour s'assurer que les comparaisons d'ID fonctionnent.
     *
     * @var array
     */
    protected $casts = [
        'echantillon_enquete_id' => 'integer',
        'user_id' => 'integer',
    ];

    // Relations

    /**
     * Obtenir l'utilisateur qui a effectué le changement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir l'échantillon concerné.
     */
    public function echantillonEnquete(): BelongsTo
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }
}
