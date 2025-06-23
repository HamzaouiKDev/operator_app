<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelephoneEntreprise extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'telephones_entreprises';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'contact_id',
        'numero',
        'source',
        'est_primaire',
        'etat_verification',
        'derniere_verification_at',
    ];

    /**
     * Utiliser un format de date non ambigu pour la compatibilité avec SQL Server.
     *
     * @var string
     */
    protected $dateFormat = 'Ymd H:i:s';

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'est_primaire' => 'boolean',
        'derniere_verification_at' => 'datetime',
    ];

    /**
     * Obtenir l'entreprise à laquelle ce numéro de téléphone appartient.
     */
    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Obtenir le contact auquel ce numéro de téléphone peut être spécifiquement associé.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(ContactEntreprise::class, 'contact_id');
    }
}
