<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $fillable = [
        'code_national', 'nom_entreprise', 'libelle_activite', 'gouvernorat',
        'numero_rue', 'nom_rue', 'ville', 'statut', 'adresse_cnss', 'localite_cnss',
    ];

    public function telephones()
    {
        return $this->hasMany(TelephoneEntreprise::class);
    }

    public function emails()
    {
        return $this->hasMany(EmailEntreprise::class);
    }

    public function contacts()
    {
        return $this->hasMany(ContactEntreprise::class);
    }

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'entreprise_id');
    }
}
?>