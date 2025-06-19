<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Gouvernorat;

class GouvernoratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Données des gouvernorats avec leurs IDs spécifiques
        $gouvernorats = [
            ['id' => 11, 'nom' => 'Tunis'],
            ['id' => 12, 'nom' => 'Ariana'],
            ['id' => 13, 'nom' => 'Ben Arous'],
            ['id' => 14, 'nom' => 'Manouba'],
            ['id' => 15, 'nom' => 'Nabeul'],
            ['id' => 16, 'nom' => 'Zaghouan'],
            ['id' => 17, 'nom' => 'Bizerte'],
            ['id' => 21, 'nom' => 'Béja'],
            ['id' => 22, 'nom' => 'Jendouba'],
            ['id' => 23, 'nom' => 'Le Kef'],
            ['id' => 24, 'nom' => 'Siliana'],
            ['id' => 31, 'nom' => 'Sousse'],
            ['id' => 32, 'nom' => 'Monastir'],
            ['id' => 33, 'nom' => 'Mahdia'],
            ['id' => 34, 'nom' => 'Sfax'],
            ['id' => 41, 'nom' => 'Kairouan'],
            ['id' => 42, 'nom' => 'Kasserine'],
            ['id' => 43, 'nom' => 'Sidi Bouzid'],
            ['id' => 51, 'nom' => 'Gabès'],
            ['id' => 52, 'nom' => 'Médenine'],
            ['id' => 53, 'nom' => 'Tataouine'],
            ['id' => 61, 'nom' => 'Gafsa'],
            ['id' => 62, 'nom' => 'Tozeur'],
            ['id' => 63, 'nom' => 'Kébili'],
        ];

        // Désactiver temporairement la protection contre l'assignation de masse
        Gouvernorat::unguard();

        // Activer IDENTITY_INSERT pour SQL Server
        DB::unprepared('SET IDENTITY_INSERT gouvernorats ON');

        // Insérer les données
        foreach ($gouvernorats as $gouvernorat) {
            Gouvernorat::updateOrCreate(['id' => $gouvernorat['id']], $gouvernorat);
        }

        // Désactiver IDENTITY_INSERT
        DB::unprepared('SET IDENTITY_INSERT gouvernorats OFF');

        // Réactiver la protection
        Gouvernorat::reguard();
    }
}
