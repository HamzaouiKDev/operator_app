<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property int $enquete_id
 * @property string $titre
 * @property string|null $description
 * @property string|null $url_enq
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Enquete $enquete
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereEnqueteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereTitre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|QuestionnaireEnquete whereUrlEnq($value)
 * @mixin \Eloquent
 */
class QuestionnaireEnquete extends Model
{
    protected $table = 'questionnaires_enquetes';

    protected $fillable = ['enquete_id', 'titre', 'description'];

    public function enquete()
    {
        return $this->belongsTo(Enquete::class, 'enquete_id');
    }
}