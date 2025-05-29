<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('telephones_entreprises', function (Blueprint $table) {
            // Vous pouvez ajuster les valeurs possibles pour 'etat_verification'
            $table->string('etat_verification')->nullable()->after('est_primaire')->comment('Statut de vérification du numéro: valide, faux_numero, pas_programme, ne_pas_deranger, non_verifie');
            $table->timestamp('derniere_verification_at')->nullable()->after('etat_verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telephones_entreprises', function (Blueprint $table) {
            $table->dropColumn('etat_verification');
            $table->dropColumn('derniere_verification_at');
        });
    }
};