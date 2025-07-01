<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * The storage format of the model's date columns.
     *
     * On force ici le format de date que SQL Server comprend sans ambiguïté.
     *
     * @var string
     */
     protected $dateFormat = 'Ymd H:i:s';
}