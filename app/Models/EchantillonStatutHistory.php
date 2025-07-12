<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EchantillonStatutHistory extends Model
{
    use HasFactory;

    protected $table = 'echantillon_statut_histories';
    protected $fillable = [
        'echantillon_enquete_id',
        'user_id',
        'ancien_statut',
        'nouveau_statut',
        'commentaire',
    ];
    protected $dateFormat = 'Ymd H:i:s';
    protected $casts = [
        'echantillon_enquete_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function echantillonEnquete(): BelongsTo
    {
        return $this->belongsTo(EchantillonEnquete::class, 'echantillon_enquete_id');
    }

    // La fonction statusHistories() a été SUPPRIMÉE d'ici.
}