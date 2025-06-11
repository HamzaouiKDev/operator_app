<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $entreprise_id
 * @property int|null $contact_id
 * @property string $numero
 * @property string|null $source
 * @property bool $est_primaire
 * @property string|null $etat_verification Statut de vérification du numéro: valide, faux_numero, pas_programme, ne_pas_deranger, non_verifie
 * @property \Illuminate\Support\Carbon|null $derniere_verification_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\ContactEntreprise|null $contact
 * @property-read \App\Models\Entreprise $entreprise
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereDerniereVerificationAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereEstPrimaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereEtatVerification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereNumero($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TelephoneEntreprise whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TelephoneEntreprise extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * (Déjà correct dans votre version)
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
        'contact_id', // ⬅️ **AJOUTÉ ICI** : Important pour lier au contact
        'numero',
        'source',
        'est_primaire',
        'etat_verification',
        'derniere_verification_at',
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     * (Déjà correct dans votre version)
     * @var array<string, string>
     */
    protected $casts = [
        'est_primaire' => 'boolean',
        'derniere_verification_at' => 'datetime',
    ];

    /**
     * Obtenir l'entreprise à laquelle ce numéro de téléphone appartient.
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Obtenir le contact auquel ce numéro de téléphone peut être spécifiquement associé.
     * (Assurez-vous que le nom du modèle 'ContactEntreprise' correspond bien à votre modèle pour la table 'contact_entreprises')
     */
    public function contact()
    {
        // Remplacez 'ContactEntreprise::class' si votre modèle de contact a un autre nom (par exemple, 'Contact::class')
        return $this->belongsTo(ContactEntreprise::class, 'contact_id');
    }
}