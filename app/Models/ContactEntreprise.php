<?php

namespace App\Models;

use App\Models\Gouvernorat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContactEntreprise extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'contact_entreprises';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'civilite',
        'prenom',
        'nom',
        'email',
        'telephone',
        'poste',
    ];

    /**
     * Utiliser un format de date non ambigu pour la compatibilité avec SQL Server.
     *
     * @var string
     */
    protected $dateFormat = 'Ymd H:i:s';

    /**
     * Obtenir l'entreprise à laquelle ce contact appartient.
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Obtenir tous les enregistrements de numéros de téléphone qui sont spécifiquement associés à ce contact.
     */
    public function telephonesDetail()
    {
        return $this->hasMany(TelephoneEntreprise::class, 'contact_id');
    }
}
