<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquete extends Model
{
    protected $table = 'enquetes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titre',
        'description',
        'statut',
        'titre_mail',
        'corps_mail',
        'piece_jointe_path', // <-- CHAMP AJOUTÉ
        'date_debut',
        'date_fin',
    ];

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'enquete_id');
    }

    public function questionnaires()
    {
        return $this->hasMany(QuestionnaireEnquete::class, 'enquete_id');
    }
}
