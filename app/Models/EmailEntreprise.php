<?php
// app/Models/EmailEntreprise.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailEntreprise extends Model
{
    use HasFactory;

    protected $table = 'emails_entreprises';

    protected $fillable = [
        'entreprise_id',
        'email',
        'source',
        'est_primaire'
    ];

    protected $casts = [
        'est_primaire' => 'boolean'
    ];

    // Relation avec l'entreprise
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }
}
