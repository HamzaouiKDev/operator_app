<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiUserSeeder extends Seeder
{
    /**
     * Exécute le seeder pour créer l'utilisateur API et son token.
     */
    public function run(): void
    {
        $this->command->line('---------------------------------------');
        $this->command->line('Exécution du ApiUserSeeder (Mode de compatibilité SQL Server)...');

        $apiUser = User::firstOrCreate(
            ['email' => 'api.user@ins.tn'],
            [
                'name' => 'API User',
                'password' => Hash::make('p@ssw@rd2025'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Utilisateur API trouvé ou créé : ' . $apiUser->email);

        $tokenName = 'default-api-token';

        if ($apiUser->tokens()->where('name', $tokenName)->doesntExist()) {
            
            $this->command->line("Génération d'un nouveau token nommé '{$tokenName}'...");

            // ======================================================================
            // ==== DÉBUT DE LA NOUVELLE LOGIQUE D'INSERTION MANUELLE ====
            // ======================================================================

            // 1. On génère la partie "visible" du token
            $plainTextToken = Str::random(40);

            // 2. On crypte cette partie pour la stocker dans la base de données (comme le fait Sanctum)
            $hashedToken = hash('sha256', $plainTextToken);

            // 3. On utilise le Query Builder pour insérer les données directement
            //    et on utilise DB::raw('GETUTCDATE()') pour que ce soit SQL Server
            //    lui-même qui gère la date, sans aucune conversion.
            $tokenId = DB::table('personal_access_tokens')->insertGetId([
                'name' => $tokenName,
                'token' => $hashedToken,
                'abilities' => '["*"]', // Doit être une chaîne JSON
                'tokenable_type' => 'App\\Models\\User',
                'tokenable_id' => $apiUser->id,
                'expires_at' => null, // On confirme que c'est bien null
                'created_at' => DB::raw('GETUTCDATE()'), // Ordre direct à SQL Server
                'updated_at' => DB::raw('GETUTCDATE()'), // Ordre direct à SQL Server
            ]);

            // 4. On reconstitue le token complet (ID + token en clair) pour l'affichage
            $fullTokenForDisplay = $tokenId . '|' . $plainTextToken;
            
            // ======================================================================
            // ==== FIN DE LA NOUVELLE LOGIQUE D'INSERTION MANUELLE ====
            // ======================================================================


            // On affiche le token complet
            $this->command->info("Token d'API généré avec succès.");
            $this->command->line("Copiez cette valeur complète pour vos tests API :");
            $this->command->warn($fullTokenForDisplay);

        } else {
            $this->command->info("Un token nommé '{$tokenName}' existe déjà pour cet utilisateur.");
        }

        $this->command->line('ApiUserSeeder terminé.');
        $this->command->line('---------------------------------------');
    }
}