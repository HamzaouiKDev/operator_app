<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // N'oubliez pas d'importer la façade DB

class GouvernoratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optionnel : Vous pouvez décommenter la ligne suivante si vous voulez vider
        // la table avant de la remplir à nouveau, utile si vous ne faites pas
        // toujours 'php artisan migrate:fresh'.
        // DB::table('gouvernorats')->truncate();

        DB::table('gouvernorats')->insert([
            // TOUTES CES CLÉS DOIVENT ÊTRE 'nom' pour correspondre à votre migration
            ['nom' => 'Ariana', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Béja', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Ben Arous', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Bizerte', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Gabès', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Gafsa', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Jendouba', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Kairouan', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Kasserine', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Kébili', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Le Kef', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Mahdia', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Manouba', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Médenine', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Monastir', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Nabeul', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Sfax', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Sidi Bouzid', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Siliana', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Sousse', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Tataouine', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Tozeur', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Tunis', 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'Zaghouan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}