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
        Schema::create('echantillons_enquetes', function (Blueprint $table) {
            $table->id();

            // Clés étrangères
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->foreignId('enquete_id')->constrained('enquetes')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->nullable()->constrained('users')->onDelete('set null');

            // Champs de statut et de priorité
            $table->string('statut')->default('non traite');
            $table->string('priorite')->nullable();

            // Champ de commentaire
            $table->text('commentaire')->nullable();
            
            // Champs de date
            $table->dateTime('date_attribution')->nullable();
            $table->dateTime('date_mise_a_jour')->nullable();
            $table->dateTime('date_liberation')->nullable();
            $table->dateTime('date_traitement')->nullable();

            // Timestamps par défaut (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echantillons_enquetes');
    }
};
