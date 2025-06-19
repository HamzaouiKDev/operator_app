<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use DateTimeInterface;
use Carbon\Carbon; // Assurez-vous d'importer Carbon ici

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    
     protected $dateFormat = 'Ymd H:i:s';

}