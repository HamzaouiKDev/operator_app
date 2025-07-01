<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquete extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'titre',
    'description',
    'statut',
    'titre_mail_ar',       // Nouveau
    'corps_mail_ar',       // Nouveau
    'titre_mail_fr',       // Nouveau
    'corps_mail_fr',       // Nouveau
    'piece_jointe_path',
    'date_debut',
    'date_fin',
];
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Utiliser un format de date non ambigu pour la compatibilité avec SQL Server.
     *
     * @var string
     */
    protected $dateFormat = 'Ymd H:i:s';

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'enquete_id');
    }
}
