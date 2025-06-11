<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Ajout pour le type hinting

/**
 * 
 *
 * @property int $id
 * @property int $echantillon_enquete_id
 * @property int $utilisateur_id
 * @property string|null $note
 * @property string $cause_suivi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\EchantillonEnquete $echantillonEnquete
 * @property-read \App\Models\User $utilisateur
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereCauseSuivi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereEchantillonEnqueteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Suivi whereUtilisateurId($value)
 * @mixin \Eloquent
 */
class Suivi extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     * Laravel l'infère correctement comme 'suivis' à partir du nom de la classe 'Suivi',
     * mais vous pouvez le spécifier explicitement si vous le souhaitez :
     * protected $table = 'suivis';
     */

    /**
     * Les attributs qui sont assignables en masse.
     * Corrigé pour correspondre à la nouvelle structure de la table.
     */
    protected $fillable = [
        'echantillon_enquete_id',
        'utilisateur_id',
        'note',          // ✅ AJOUTÉ (remplace commentaire)
        'cause_suivi',   // ✅ AJOUTÉ (devient le descripteur principal)
    ];

    /**
     * Les attributs qui doivent être castés.
     * 'created_at' et 'updated_at' sont automatiquement castés en instances Carbon
     * car la table utilise timestamps().
     * Si 'date_suivi' était conservé, il serait ici.
     */
    // protected $casts = [
    //     // 'date_suivi' => 'datetime', // Plus nécessaire si on utilise created_at
    // ];

    /**
     * Récupère l'échantillon d'enquête associé à ce suivi.
     */
    public function echantillonEnquete(): BelongsTo
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    /**
     * Récupère l'utilisateur qui a effectué ce suivi.
     */
    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    
}