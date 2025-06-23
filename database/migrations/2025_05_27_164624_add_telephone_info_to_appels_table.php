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
        Schema::table('appels', function (Blueprint $table) {
            // Ligne corrigée
$table->foreignId('telephone_utilise_id')->nullable()->constrained('telephones_entreprises');
            
            // Ajoute une colonne pour stocker le numéro exact qui a été composé
            $table->string('numero_compose')->nullable()->after('telephone_utilise_id');
            
            // Ajoute une colonne pour stocker le statut du numéro au moment de l'appel
            $table->string('statut_numero_au_moment_appel')->nullable()->after('numero_compose');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appels', function (Blueprint $table) {
            // Important de retirer les contraintes de clé étrangère avant de supprimer la colonne
            $table->dropForeign(['telephone_utilise_id']); // Nom de la contrainte par défaut: appels_telephone_utilise_id_foreign
            
            $table->dropColumn('telephone_utilise_id');
            $table->dropColumn('numero_compose');
            $table->dropColumn('statut_numero_au_moment_appel');
        });
    }
};