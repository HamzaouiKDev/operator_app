<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireEnquete extends Model
{
    use HasFactory;

    protected $table = 'questionnaires_enquetes';

    protected $fillable = ['enquete_id', 'titre', 'description'];

    /**
     * Utiliser un format de date non ambigu pour la compatibilité avec SQL Server.
     *
     * @var string
     */
    protected $dateFormat = 'Ymd H:i:s';

    public function enquete()
    {
        return $this->belongsTo(Enquete::class, 'enquete_id');
    }
}
