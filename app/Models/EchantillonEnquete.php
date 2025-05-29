<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EchantillonEnquete extends Model
{
    use HasFactory;

    protected $table = 'echantillons_enquetes';

    protected $fillable = [
        'entreprise_id',
        'enquete_id',
        'statut',
        'priorite',
        'utilisateur_id',      // ✅ IMPORTANT - Ajouté
        'date_attribution',
        'date_mise_a_jour',
        'date_liberation'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'date_attribution',
        'date_mise_a_jour',
        'date_liberation'
    ];

    // Relations
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    public function enquete()
    {
        return $this->belongsTo(Enquete::class, 'enquete_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}
