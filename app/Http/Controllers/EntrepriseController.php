<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $table = 'entreprises';

    protected $fillable = [
        'code_national',
        'nom_entreprise',
        'libelle_activite',
        'gouvernorat',
        'numero_rue',
        'nom_rue',
        'ville',
        'statut',
        'adresse_cnss',
        'localite_cnss',
    ];
}