<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echantillons_enquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->foreignId('enquete_id')->constrained('enquetes')->onDelete('cascade');
            $table->string('statut');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echantillons_enquetes');
    }
};