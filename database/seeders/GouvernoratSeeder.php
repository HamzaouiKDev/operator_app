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
            ['id' => '12','nom' => 'Ariana', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '21','nom' => 'Béja', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '13','nom' => 'Ben Arous', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '17','nom' => 'Bizerte', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '51','nom' => 'Gabès', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '61','nom' => 'Gafsa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '22','nom' => 'Jendouba', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '41','nom' => 'Kairouan', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '42','nom' => 'Kasserine', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '63','nom' => 'Kébili', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '23','nom' => 'Le Kef', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '33','nom' => 'Mahdia', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '14','nom' => 'Manouba', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '52','nom' => 'Médenine', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '32','nom' => 'Monastir', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '15','nom' => 'Nabeul', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '34','nom' => 'Sfax', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '43','nom' => 'Sidi Bouzid', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '24','nom' => 'Siliana', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '31','nom' => 'Sousse', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '53','nom' => 'Tataouine', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '62','nom' => 'Tozeur', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '11','nom' => 'Tunis', 'created_at' => now(), 'updated_at' => now()],
            ['id' => '16','nom' => 'Zaghouan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}