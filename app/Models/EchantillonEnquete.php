<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $entreprise_id
 * @property int $enquete_id
 * @property string $statut
 * @property string|null $priorite
 * @property int|null $utilisateur_id
 * @property string|null $date_attribution
 * @property string|null $date_mise_a_jour
 * @property string|null $date_liberation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Enquete $enquete
 * @property-read \App\Models\Entreprise $entreprise
 * @property-read \App\Models\User|null $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereDateAttribution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereDateLiberation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereDateMiseAJour($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereEnqueteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete wherePriorite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EchantillonEnquete whereUtilisateurId($value)
 * @mixin \Eloquent
 */
class EchantillonEnquete extends Model
{
    use HasFactory;

    protected $table = 'echantillons_enquetes';

    protected $fillable = [
        'entreprise_id',
        'enquete_id',
        'statut',
        'priorite',
        'utilisateur_id',      // ✅ IMPORTANT - Ajouté
        'date_attribution',
        'date_mise_a_jour',
        'date_liberation'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_attribution',
        'date_mise_a_jour',
        'date_liberation'
    ];

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function enquete()
    {
        return $this->belongsTo(Enquete::class, 'enquete_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}
