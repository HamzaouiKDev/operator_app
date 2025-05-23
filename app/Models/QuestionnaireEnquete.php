<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireEnquete extends Model
{
    protected $table = 'questionnaires_enquetes';

    protected $fillable = ['enquete_id', 'titre', 'description'];

    public function enquete()
    {
        return $this->belongsTo(Enquete::class, 'enquete_id');
    }
}