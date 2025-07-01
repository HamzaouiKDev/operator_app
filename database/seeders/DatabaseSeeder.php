<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;

// Import de tous vos modèles
use App\Models\Appel;
use App\Models\Suivi;
use App\Models\Enquete;
use App\Models\Entreprise;
use App\Models\RendezVous;
use App\Models\Gouvernorat;
use Faker\Factory as Faker;
use App\Models\EmailEntreprise;
use Illuminate\Database\Seeder;
use App\Models\ContactEntreprise;
use App\Models\EchantillonEnquete;
use App\Models\TelephoneEntreprise;
use Database\Seeders\ApiUserSeeder;
use App\Models\QuestionnaireEnquete;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // On appelle d'abord les seeders de base.
        $this->call([
            RolesAndUsersSeeder::class,
            GouvernoratSeeder::class,
            ApiUserSeeder::class, // <-- Ajoutez cette ligne
        ]);

        // On récupère les données essentielles
        $teleoperateurs = User::role('Téléopérateur')->get();
        $gouvernoratIds = Gouvernorat::pluck('id')->all();

        // Vérifications pour s'assurer que les données de base existent
        if ($teleoperateurs->isEmpty() || empty($gouvernoratIds)) {
            $this->command->error('Données de base manquantes (Téléopérateurs ou Gouvernorats). Assurez-vous que les seeders RolesAndUsersSeeder et GouvernoratSeeder ont bien fonctionné. Arrêt du seeding.');
            return;
        }

        
       

            
        
        
       

        
    }
}
