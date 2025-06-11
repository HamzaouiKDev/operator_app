<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $echantillon_enquete_id
 * @property int $utilisateur_id
 * @property int|null $telephone_utilise_id
 * @property string|null $numero_compose
 * @property string|null $statut_numero_au_moment_appel
 * @property \Illuminate\Support\Carbon $heure_debut
 * @property \Illuminate\Support\Carbon $heure_fin
 * @property string $statut
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EchantillonEnquete $echantillonEnquete
 * @property-read \App\Models\TelephoneEntreprise|null $telephoneUtilise
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereEchantillonEnqueteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereHeureDebut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereHeureFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereNumeroCompose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereStatutNumeroAuMomentAppel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereTelephoneUtiliseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Appel whereUtilisateurId($value)
 * @mixin \Eloquent
 */
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