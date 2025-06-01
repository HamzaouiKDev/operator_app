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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('echantillon_enquete_id')->constrained('echantillons_enquetes')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('heure_rdv'); // Champ unique pour l'heure du rendez-vous
            $table->string('contact_rdv'); // Nouveau champ pour les informations de contact du RDV
            $table->string('statut')->default('planifie'); // Champ statut avec "planifie" comme valeur par défaut
            $table->text('notes')->nullable();
            $table->timestamps(); // Ajoute created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};