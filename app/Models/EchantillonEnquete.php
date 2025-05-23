<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EchantillonEnquete extends Model
{
    protected $table = 'echantillons_enquetes'; // Correct table name

    protected $fillable = [
        'entreprise_id',
        'enquete_id',
        'statut',
        'priorite',
    ];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }
}