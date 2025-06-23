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
        Schema::create('echantillon_statut_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('echantillon_enquete_id')->constrained('echantillons_enquetes')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('ancien_statut');
            $table->string('nouveau_statut');
            $table->text('commentaire')->nullable();
            $table->timestamps(); // Crée les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echantillon_statut_histories');
    }
};