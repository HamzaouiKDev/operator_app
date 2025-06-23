<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Importez le modèle User
use Illuminate\Support\Facades\Hash; // Pour hasher le mot de passe

class ApiUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créez l'utilisateur API
        $apiUser = User::firstOrCreate(
            ['email' => 'api.user@example.com'], // Cherche par email pour éviter les doublons
            [
                'name' => 'API User',
                'password' => Hash::make('secret_api_password'), // Choisissez un mot de passe fort
                // Ajoutez d'autres champs si votre modèle User en requiert (ex: email_verified_at)
                'email_verified_at' => now(), // Important pour certains systèmes Laravel
            ]
        );

        // Optionnel : attribuez-lui un rôle si vous utilisez Spatie Permission pour l'API aussi
        // Assurez-vous que le rôle 'ApiAccess' existe ou créez-le dans RolesAndUsersSeeder.php
        // $apiUser->assignRole('ApiAccess');

        $this->command->info('Utilisateur API créé ou mis à jour : ' . $apiUser->email);
    }
}