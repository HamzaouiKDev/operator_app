<?php
// app/Models/Entreprise.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entreprise extends Model
{
    use HasFactory;

    // ... vos attributs existants

    // Relations
    public function telephones()
    {
        return $this->hasMany(TelephoneEntreprise::class, 'entreprise_id');
    }

    public function contacts()
    {
        return $this->hasMany(ContactEntreprise::class, 'entreprise_id');
    }

    // **RELATION AVEC EmailEntreprise**
    public function emails()
    {
        return $this->hasMany(EmailEntreprise::class, 'entreprise_id'); // ✅ CORRECT
    }

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'entreprise_id');
    }
}
