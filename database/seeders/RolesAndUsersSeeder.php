<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// CORRECTION : Utiliser le modèle Role de l'application
use App\Models\Role;
use App\Models\User;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vider le cache des permissions pour éviter les problèmes
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Créer les rôles s'ils n'existent pas déjà
        // Cette ligne utilisera maintenant App\Models\Role, qui a la correction pour le format de date
        $adminRole = Role::firstOrCreate(['name' => 'Admin'], ['guard_name' => 'web']);
        $teleopRole = Role::firstOrCreate(['name' => 'Téléopérateur'], ['guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'Superviseur']);

        // 2. Créer l'utilisateur Administrateur (un compte fixe et connu)
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@ins.tn'], // On le cherche par son email
            [ // Données à utiliser s'il n'existe pas
                'name' => 'Administrateur',
                'password' => bcrypt('password') // Mot de passe par défaut
            ]
        );
        // On lui assigne UNIQUEMENT le rôle Admin
        $adminUser->syncRoles($adminRole);

       
        
    }
}
