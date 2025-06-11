<?php
// app/Models/EmailEntreprise.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $entreprise_id
 * @property string $email
 * @property string|null $source
 * @property bool $est_primaire
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Entreprise $entreprise
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereEstPrimaire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailEntreprise whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailEntreprise extends Model
{
    use HasFactory;

    protected $table = 'emails_entreprises';

    protected $fillable = [
        'entreprise_id',
        'email',
        'source',
        'est_primaire'
    ];

    protected $casts = [
        'est_primaire' => 'boolean'
    ];

    // Relation avec l'entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }
}
