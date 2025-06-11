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
use App\Models\Gouvernorat; // <-- ESSENTIEL : Importe le modèle Gouvernorat

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // On appelle d'abord les seeders de base en premier.
        // GouvernoratSeeder DOIT être appelé avant de créer des entreprises.
        $this->call([
            RolesAndUsersSeeder::class,
            GouvernoratSeeder::class, // <-- ESSENTIEL : Appelle le seeder des gouvernorats pour qu'ils soient créés et remplis
        ]);

        // On récupère SEULEMENT les téléopérateurs et les ID des gouvernorats existants
        $teleoperateurs = User::role('Téléopérateur')->get();
        // <-- ESSENTIEL : Récupère les IDs des gouvernorats DE LA BASE DE DONNÉES
        $gouvernoratIds = Gouvernorat::pluck('id')->all();

        // Si des données de base manquent, on arrête le seeding
        if ($teleoperateurs->isEmpty()) {
            $this->command->warn('Aucun téléopérateur trouvé. Vérifiez RolesAndUsersSeeder. Arrêt du seeding des données métier.');
            return;
        }
        if (empty($gouvernoratIds)) { // <-- ESSENTIEL : Vérifie si des ID de gouvernorats ont été trouvés
            $this->command->error('Aucun gouvernorat trouvé en base de données. Assurez-vous que GouvernoratSeeder a bien fonctionné. Arrêt du seeding.');
            return;
        }

        $faker = Faker::create('fr_FR');

        $libellesActivite = [
            'Conseil en gestion', 'Développement logiciel', 'Commerce de détail',
            'Fabrication industrielle', 'Services financiers', 'Transport et logistique',
            'Restauration', 'Santé et bien-être', 'Éducation et formation',
            'Construction', 'Marketing et publicité', 'Tourisme et hôtellerie'
        ];
        // La liste $gouvernorats sous forme de tableau de chaînes de caractères n'est PLUS NÉCESSAIRE ici.
        // Nous utilisons les IDs numériques récupérés de la base de données.
        // $gouvernorats = ['Tunis', 'Ariana', ...];
        $statutsEchantillons = ['nouveau', 'en_attente', 'en_cours', 'termine', 'annule'];
        $causesSuivi = [
            'Réponse absente', 'Personne non adéquate', 'Rappel demandé par client',
            'Information manquante', 'Autre'
        ];

        // Créer une seule enquête
        $enquete = Enquete::create([
            'titre' => $faker->sentence(4),
            'description' => $faker->paragraph,
            'date_debut' => $faker->dateTimeBetween('-1 month', 'now'),
            'date_fin' => $faker->dateTimeBetween('now', '+1 month'),
            'statut' => $faker->randomElement(['en_cours', 'terminee', 'planifiee'])
        ]);
        for ($j = 0; $j < rand(1, 3); $j++) {
            QuestionnaireEnquete::create([
                'enquete_id' => $enquete->id,
                'titre' => $faker->sentence(3),
                'description' => $faker->paragraph
            ]);
        }

        // Créer 30 entreprises
        for ($i = 0; $i < 30; $i++) {
            $entreprise = Entreprise::create([
                'code_national' => $faker->unique()->numerify('NAT-######'),
                'nom_entreprise' => $faker->unique()->company,
                'libelle_activite' => $faker->randomElement($libellesActivite),
                // <-- ESSENTIEL : Utilise 'gouvernorat_id' et un ID NUMÉRIQUE du tableau $gouvernoratIds
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

            $echantillon = EchantillonEnquete::create([
                'entreprise_id' => $entreprise->id,
                'enquete_id' => $enquete->id,
                'statut' => $faker->randomElement($statutsEchantillons),
                'priorite' => $faker->randomElement(['basse', 'moyenne', 'haute'])
            ]);

            for ($k = 0; $k < rand(1, 3); $k++) {
                RendezVous::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $teleoperateurs->random()->id,
                    'heure_rdv' => $faker->dateTimeBetween('now', '+1 week'),
                    'contact_rdv' => $faker->name(),
                    'statut' => $faker->randomElement(['planifie', 'confirme', 'annule']),
                    'notes' => $faker->optional()->paragraph
                ]);
            }
            for ($k = 0; $k < rand(1, 3); $k++) {
                Suivi::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $teleoperateurs->random()->id,
                    'cause_suivi' => $faker->randomElement($causesSuivi),
                    'note' => $faker->optional()->paragraph
                ]);
            }
            for ($k = 0; $k < rand(1, 3); $k++) {
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