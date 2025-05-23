<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelephoneEntreprise extends Model
{
    protected $table = 'telephones_entreprises';

    protected $fillable = ['entreprise_id', 'numero', 'source', 'est_primaire'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }
}