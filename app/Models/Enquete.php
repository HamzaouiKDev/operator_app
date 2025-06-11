<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $titre
 * @property string|null $description
 * @property string $date_debut
 * @property string $date_fin
 * @property string $statut
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EchantillonEnquete> $echantillons
 * @property-read int|null $echantillons_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\QuestionnaireEnquete> $questionnaires
 * @property-read int|null $questionnaires_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereDateDebut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereDateFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereStatut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereTitre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Enquete whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Enquete extends Model
{
    protected $table = 'enquetes';

    protected $fillable = ['titre', 'description', 'date_debut', 'date_fin', 'statut'];

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'enquete_id');
    }

    public function questionnaires()
    {
        return $this->hasMany(QuestionnaireEnquete::class, 'enquete_id');
    }
}