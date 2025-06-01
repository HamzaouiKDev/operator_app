<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Appel;
use App\Models\Suivi;
use App\Models\Enquete;
use App\Models\Entreprise;
use App\Models\RendezVous;
use Faker\Factory as Faker;
use App\Models\EmailEntreprise;
use Illuminate\Database\Seeder;
use App\Models\ContactEntreprise;
use App\Models\EchantillonEnquete;
use App\Models\TelephoneEntreprise;
use App\Models\QuestionnaireEnquete;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Liste personnalisée de libellés d'activité en français
        $libellesActivite = [
            'Conseil en gestion', 'Développement logiciel', 'Commerce de détail',
            'Fabrication industrielle', 'Services financiers', 'Transport et logistique',
            'Restauration', 'Santé et bien-être', 'Éducation et formation',
            'Construction', 'Marketing et publicité', 'Tourisme et hôtellerie',
        ];

        // Liste personnalisée de gouvernorats tunisiens
        $gouvernorats = [
            'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan', 'Bizerte',
            'Béja', 'Jendouba', 'Le Kef', 'Siliana', 'Kairouan', 'Kasserine', 'Sidi Bouzid',
            'Sousse', 'Monastir', 'Mahdia', 'Sfax', 'Gafsa', 'Tozeur', 'Kebili', 'Gabès',
            'Médenine', 'Tataouine',
        ];

        // Liste de statuts pour échantillons d'enquêtes
        $statutsEchantillons = ['nouveau', 'en_attente', 'en_cours', 'termine', 'annule'];

        // Liste de causes de suivi (basée sur votre formulaire dans index.blade.php)
        $causesSuivi = [
            'Réponse absente',
            'Personne non adéquate',
            'Rappel demandé par client',
            'Information manquante',
            'Autre',
        ];

        // 1. Créer 5 utilisateurs
        $utilisateurs = collect();
        for ($i = 0; $i < 5; $i++) {
            $utilisateur = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $utilisateurs->push($utilisateur);
        }

        // 2. Créer une seule enquête
        $enquete = Enquete::create([
            'titre' => $faker->sentence(4),
            'description' => $faker->paragraph,
            'date_debut' => $faker->dateTimeBetween('-1 month', 'now'),
            'date_fin' => $faker->dateTimeBetween('now', '+1 month'),
            'statut' => $faker->randomElement(['en_cours', 'terminee', 'planifiee']),
        ]);

        // Créer 1 à 3 questionnaires pour cette enquête
        for ($j = 0; $j < rand(1, 3); $j++) {
            QuestionnaireEnquete::create([
                'enquete_id' => $enquete->id,
                'titre' => $faker->sentence(3),
                'description' => $faker->paragraph,
            ]);
        }

        // Liste pour stocker les noms d'entreprise uniques
        $nomsEntreprises = collect();

        // 3. Créer 30 entreprises avec des noms uniques
        for ($i = 0; $i < 30; $i++) {
            // Générer un nom d'entreprise unique
            do {
                $nomEntreprise = $faker->company;
            } while ($nomsEntreprises->contains($nomEntreprise));

            $nomsEntreprises->push($nomEntreprise);

            $entreprise = Entreprise::create([
                'code_national' => $faker->unique()->numerify('NAT-######'),
                'nom_entreprise' => $nomEntreprise,
                'libelle_activite' => $faker->randomElement($libellesActivite),
                'gouvernorat' => $faker->randomElement($gouvernorats),
                'numero_rue' => $faker->buildingNumber,
                'nom_rue' => $faker->streetName,
                'ville' => $faker->city,
                'statut' => $faker->randomElement(['active', 'inactive', 'en_attente']),
                'adresse_cnss' => $faker->optional()->streetAddress,
                'localite_cnss' => $faker->optional()->city,
            ]);

            // 4. Créer 1 à 3 téléphones par entreprise
            for ($j = 0; $j < rand(1, 3); $j++) {
                TelephoneEntreprise::create([
                    'entreprise_id' => $entreprise->id,
                    'numero' => $faker->phoneNumber,
                    'source' => $faker->optional()->word,
                    'est_primaire' => $j === 0,
                ]);
            }

            // 5. Créer 1 à 2 emails par entreprise
            for ($j = 0; $j < rand(1, 2); $j++) {
                EmailEntreprise::create([
                    'entreprise_id' => $entreprise->id,
                    'email' => $faker->unique()->companyEmail,
                    'source' => $faker->optional()->word,
                    'est_primaire' => $j === 0,
                ]);
            }

            // 6. Créer 1 à 3 contacts par entreprise
            for ($j = 0; $j < rand(1, 3); $j++) {
                ContactEntreprise::create([
                    'entreprise_id' => $entreprise->id,
                    'civilite' => $faker->randomElement(['M.', 'Mme', null]),
                    'prenom' => $faker->firstName,
                    'nom' => $faker->lastName,
                    'email' => $faker->optional()->safeEmail,
                    'telephone' => $faker->optional()->phoneNumber,
                    'poste' => $faker->jobTitle,
                ]);
            }

            // 7. Créer un seul échantillon d'enquête par entreprise (lié à la seule enquête)
            $echantillon = EchantillonEnquete::create([
                'entreprise_id' => $entreprise->id,
                'enquete_id' => $enquete->id,
                'statut' => $faker->randomElement($statutsEchantillons),
                'priorite' => $faker->randomElement(['basse', 'moyenne', 'haute']),
            ]);

            // 8. Créer 1 à 3 rendez-vous par échantillon
            for ($k = 0; $k < rand(1, 3); $k++) {
                $heureRdv = $faker->dateTimeBetween('now', '+1 week');
                RendezVous::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $utilisateurs->random()->id,
                    'heure_rdv' => $heureRdv,
                    'contact_rdv' => $faker->name(),
                    'statut' => $faker->randomElement(['planifie', 'confirme', 'annule']),
                    'notes' => $faker->optional()->paragraph,
                ]);
            }

            // 9. Créer 1 à 3 suivis par échantillon
            for ($k = 0; $k < rand(1, 3); $k++) {
                Suivi::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $utilisateurs->random()->id,
                    'cause_suivi' => $faker->randomElement($causesSuivi),
                    'note' => $faker->optional()->paragraph,
                ]);
            }

            // 10. Créer 1 à 3 appels par échantillon
            for ($k = 0; $k < rand(1, 3); $k++) {
                $heureDebutAppel = $faker->dateTimeBetween('-1 month', 'now');
                Appel::create([
                    'echantillon_enquete_id' => $echantillon->id,
                    'utilisateur_id' => $utilisateurs->random()->id,
                    'heure_debut' => $heureDebutAppel,
                    'heure_fin' => $faker->dateTimeBetween($heureDebutAppel, $heureDebutAppel->format('Y-m-d H:i:s') . ' +1 hour'),
                    'statut' => $faker->randomElement(['termine', 'en_cours', 'echec']),
                    'notes' => $faker->optional()->paragraph,
                ]);
            }
        }
    }
}