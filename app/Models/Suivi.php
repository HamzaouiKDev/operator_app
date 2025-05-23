<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suivi extends Model
{
    protected $table = 'suivis';

    protected $fillable = ['echantillon_enquete_id', 'utilisateur_id', 'date_suivi', 'commentaire', 'resultat'];

    public function echantillonEnquete()
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}