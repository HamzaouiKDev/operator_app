<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
