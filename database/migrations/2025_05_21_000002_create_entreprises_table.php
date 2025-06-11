<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entreprises', function (Blueprint $table) {
            $table->id();
            $table->string('code_national');
            $table->string('nom_entreprise');
            $table->string('libelle_activite');
            
            // La relation professionnelle
            $table->foreignId('gouvernorat_id')->constrained('gouvernorats');
            
            $table->string('numero_rue');
            $table->string('nom_rue');
            $table->string('ville');
            $table->string('statut');
            $table->string('adresse_cnss')->nullable();
            $table->string('localite_cnss')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};