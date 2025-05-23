<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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