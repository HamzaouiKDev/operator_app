<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gouvernorat extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés massivement.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'nom',
    ];
    
   protected $dateFormat = 'Ymd H:i:s';
   

    /**
     * Indique que la clé primaire n'est pas auto-incrémentée
     * si vous définissez les ID manuellement (comme c'est le cas).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Définit la relation : Un gouvernorat A PLUSIEURS entreprises.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entreprises(): HasMany
    {
        // On précise la clé étrangère 'gouvernorat_id' dans la table 'entreprises'.
        return $this->hasMany(Entreprise::class, 'gouvernorat_id');
    }
}