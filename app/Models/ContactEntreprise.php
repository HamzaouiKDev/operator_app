<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactEntreprise extends Model
{
    protected $table = 'contact_entreprises';

    protected $fillable = [
        'entreprise_id',
        'civilite',
        'prenom',
        'nom',
        'email',
        'telephone',
        'poste',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }
}