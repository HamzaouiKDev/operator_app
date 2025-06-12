<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('enquetes', function (Blueprint $table) {
            $table->id();
            $table->string('titre'); // Titre principal de l'enquête, ex: "Enquête de satisfaction T2"
            $table->text('description')->nullable(); // Description générale de l'enquête
            $table->string('statut')->default('inactive'); // Statut de l'enquête (ex: active, inactive, terminée)
            
            // --- Champs pour le modèle d'e-mail ---
            $table->string('titre_mail')->nullable();      // Sujet de l'e-mail à envoyer
            $table->text('corps_mail')->nullable();       // Corps du message de l'email
            $table->string('piece_jointe_path')->nullable(); // Nom du fichier de la pièce jointe (ex: 'guide.pdf')

            // --- Dates de validité de l'enquête ---
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('enquetes');
    }
};
