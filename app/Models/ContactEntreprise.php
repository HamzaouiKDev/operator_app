<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // ⬅️ **AJOUTÉ**
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $entreprise_id
 * @property string|null $civilite
 * @property string $prenom
 * @property string $nom
 * @property string|null $email
 * @property string|null $telephone
 * @property string|null $poste
 * @property-read \App\Models\Entreprise $entreprise
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TelephoneEntreprise> $telephonesDetail
 * @property-read int|null $telephones_detail_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereCivilite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereEntrepriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereNom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise wherePoste($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise wherePrenom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereTelephone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactEntreprise whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ContactEntreprise extends Model
{
    use HasFactory; // ⬅️ **AJOUTÉ**

    /**
     * Le nom de la table associée au modèle.
     * (Déjà correct dans votre version)
     * @var string
     */
    protected $table = 'contact_entreprises';

    /**
     * Les attributs qui sont assignables en masse.
     * (Déjà correct dans votre version)
     * @var array<int, string>
     */
    protected $fillable = [
        'entreprise_id',
        'civilite',
        'prenom',
        'nom',
        'email',
        'telephone', // Le numéro de téléphone principal/direct du contact
        'poste',
    ];

    /**
     * Les attributs qui doivent être convertis (pas de casts spécifiques ici pour l'instant).
     *
     * @var array
     */
    // protected $casts = []; // Si vous avez besoin de casts plus tard

    /**
     * Obtenir l'entreprise à laquelle ce contact appartient.
     * (Déjà correct dans votre version)
     */
    public function entreprise()
    {
        // Si le nom du modèle Entreprise est différent, ajustez 'Entreprise::class'
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Obtenir tous les enregistrements de numéros de téléphone de la table 'telephones_entreprises'
     * qui sont spécifiquement associés à ce contact via la colonne 'contact_id'.
     * J'utilise 'telephonesDetail' pour éviter toute confusion avec l'attribut 'telephone' ci-dessus.
     */
    public function telephonesDetail()
    {
        // Assurez-vous que le nom du modèle 'TelephoneEntreprise::class' est correct.
        // Le deuxième argument 'contact_id' est la clé étrangère dans la table 'telephones_entreprises'.
        return $this->hasMany(TelephoneEntreprise::class, 'contact_id');
    }
}