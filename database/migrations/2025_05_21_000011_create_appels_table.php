<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('echantillon_enquete_id')->constrained('echantillons_enquetes')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('heure_debut');
            $table->dateTime('heure_fin');
            $table->string('statut');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appels');
    }
};