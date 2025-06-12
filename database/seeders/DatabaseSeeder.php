<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

// Import de tous vos modèles
use App\Models\User;
use App\Models\Enquete;
use App\Models\Entreprise;
use App\Models\EchantillonEnquete;
use App\Models\TelephoneEntreprise;
use App\Models\EmailEntreprise;
use App\Models\ContactEntreprise;
use App\Models\RendezVous;
use App\Models\Suivi;
use App\Models\Appel;
use App\Models\QuestionnaireEnquete;
use App\Models\Gouvernorat;

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
        ]);

        // On récupère les données essentielles
        $teleoperateurs = User::role('Téléopérateur')->get();
        $gouvernoratIds = Gouvernorat::pluck('id')->all();

        // Vérifications pour s'assurer que les données de base existent
        if ($teleoperateurs->isEmpty() || empty($gouvernoratIds)) {
            $this->command->error('Données de base manquantes (Téléopérateurs ou Gouvernorats). Assurez-vous que les seeders RolesAndUsersSeeder et GouvernoratSeeder ont bien fonctionné. Arrêt du seeding.');
            return;
        }

        $faker = Faker::create('fr_FR');

        // Créer plusieurs enquêtes (servant de modèles d'emails)
        $enquetes = collect(); // Crée une collection vide pour stocker nos enquêtes
        for ($i = 0; $i < 4; $i++) {
            $enquete = Enquete::create([
                'titre' => 'Enquête ' . $faker->word . ' ' . ($i + 1),
                'description' => $faker->paragraph,
                'statut' => $faker->randomElement(['en_cours', 'planifiee']),
                // --- Nouveaux champs pour les emails types ---
                'titre_mail' => 'Information importante concernant : ' . $faker->sentence(3),
                'corps_mail' => "Bonjour,\n\n" . $faker->realText(400) . "\n\nCordialement,\nL'équipe.",
                'date_debut' => $faker->dateTimeBetween('-1 month', 'now'),
                'date_fin' => $faker->dateTimeBetween('+1 month', '+2 months'),
            ]);

            // Ajoute l'enquête créée à notre collection
            $enquetes->push($enquete);

            // Créer des questionnaires pour chaque enquête
            for ($j = 0; $j < rand(1, 3); $j++) {
                QuestionnaireEnquete::create([
                    'enquete_id' => $enquete->id,
                    'titre' => $faker->sentence(3),
                    'description' => $faker->paragraph
                ]);
            }
        }
        
        $libellesActivite = [
            'Conseil en gestion', 'Développement logiciel', 'Commerce de détail',
            'Fabrication industrielle', 'Services financiers', 'Transport et logistique',
            'Restauration', 'Santé et bien-être', 'Éducation et formation',
            'Construction', 'Marketing et publicité', 'Tourisme et hôtellerie'
        ];
        $statutsEchantillons = ['nouveau', 'en_attente', 'en_cours', 'termine', 'annule'];
        $causesSuivi = [
            'Réponse absente', 'Personne non adéquate', 'Rappel demandé par client',
            'Information manquante', 'Autre'
        ];

        // Créer 30 entreprises
        for ($i = 0; $i < 30; $i++) {
            $entreprise = Entreprise::create([
                'code_national' => $faker->unique()->numerify('NAT-######'),
                'nom_entreprise' => $faker->unique()->company,
                'libelle_activite' => $faker->randomElement($libellesActivite),
                'gouvernorat_id' => $faker->randomElement($gouvernoratIds),
                'numero_rue' => $faker->buildingNumber,
                'nom_rue' => $faker->streetName,
                'ville' => $faker->city,
                'statut' => $faker->randomElement(['active', 'inactive', 'en_attente']),
                'adresse_cnss' => $faker->optional()->streetAddress,
                'localite_cnss' => $faker->optional()->city,
            ]);

            // Créer les téléphones, emails, contacts, etc.
            for ($j = 0; $j < rand(1, 3); $j++) {
                TelephoneEntreprise::create([
                    'entreprise_id' => $entreprise->id,
                    'numero' => $faker->phoneNumber,
                    'source' => $faker->optional()->word,
                    'est_primaire' => $j === 0
                ]);
            }
            for ($j = 0; $j < rand(1, 2); $j++) {
                EmailEntreprise::create([
                    'entreprise_id' => $entreprise->id,
                    'email' => $faker->unique()->companyEmail,
                    'source' => $faker->optional()->word,
                    'est_primaire' => $j === 0
                ]);
            }
            for ($j = 0; $j < rand(1, 3); $j++) {
                ContactEntreprise::create([
                    'entreprise_id' => $entreprise->id,
                    'civilite' => $faker->randomElement(['M.', 'Mme', null]),
                    'prenom' => $faker->firstName,
                    'nom' => $faker->lastName,
                    'email' => $faker->optional()->safeEmail,
                    'telephone' => $faker->optional()->phoneNumber,
                    'poste' => $faker->jobTitle
                ]);
            }

            // Attribuer l'entreprise à une enquête au hasard
            $echantillon = EchantillonEnquete::create([
                'entreprise_id' => $entreprise->id,
                'enquete_id' => $enquetes->random()->id, // <-- On utilise une enquête au hasard
                'statut' => $faker->randomElement($statutsEchantillons),
                'priorite' => $faker->randomElement(['basse', 'moyenne', 'haute'])
            ]);

            // Créer des rendez-vous, suivis et appels pour chaque échantillon
            for ($k = 0; $k < rand(0, 2); $k++) {
                RendezVous::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $teleoperateurs->random()->id,
                    'heure_rdv' => $faker->dateTimeBetween('now', '+1 week'),
                    'contact_rdv' => $faker->name(),
                    'statut' => $faker->randomElement(['planifie', 'confirme', 'annule']),
                    'notes' => $faker->optional()->paragraph
                ]);
            }
            for ($k = 0; $k < rand(0, 3); $k++) {
                Suivi::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $teleoperateurs->random()->id,
                    'cause_suivi' => $faker->randomElement($causesSuivi),
                    'note' => $faker->optional()->paragraph
                ]);
            }
            for ($k = 0; $k < rand(1, 5); $k++) {
                $heureDebutAppel = $faker->dateTimeBetween('-1 month', 'now');
                Appel::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $teleoperateurs->random()->id,
                    'heure_debut' => $heureDebutAppel,
                    'heure_fin' => $faker->dateTimeBetween($heureDebutAppel, $heureDebutAppel->format('Y-m-d H:i:s') . ' +1 hour'),
                    'statut' => $faker->randomElement(['termine', 'en_cours', 'echec']),
                    'notes' => $faker->optional()->paragraph
                ]);
            }
        }
    }
}
