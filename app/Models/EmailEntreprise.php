<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailEntreprise extends Model
{
    protected $table = 'emails_entreprises';

    protected $fillable = ['entreprise_id', 'email', 'source', 'est_primaire'];

    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }
}