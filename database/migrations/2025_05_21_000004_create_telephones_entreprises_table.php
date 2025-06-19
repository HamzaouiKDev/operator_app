<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CORRECTION : La classe de migration doit être anonyme ou avoir un nom unique,
// elle ne doit PAS être une redéclaration du modèle.
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('telephones_entreprises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            
            // La colonne 'numero' est un `string` pour accepter les numéros longs.
            $table->string('numero'); 

            $table->string('source')->nullable();
            $table->boolean('est_primaire')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telephones_entreprises');
    }
};
