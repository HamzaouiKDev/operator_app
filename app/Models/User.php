<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    /**
     * Utiliser un format de date non ambigu pour la compatibilité avec SQL Server.
     *
     * @var string
     */
    protected $dateFormat = 'Ymd H:i:s';

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'utilisateur_id');
    }

    public function appels()
    {
        return $this->hasMany(Appel::class, 'utilisateur_id');
    }

    public function suivis()
    {
        return $this->hasMany(Suivi::class, 'utilisateur_id');
    }

    public function echantillons()
    {
        return $this->hasMany(EchantillonEnquete::class, 'utilisateur_id');
    }
}
