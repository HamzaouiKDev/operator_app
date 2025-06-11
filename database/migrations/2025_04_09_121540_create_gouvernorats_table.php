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
        Schema::create('gouvernorats', function (Blueprint $table) {
            $table->id(); // Colonne pour le code (ex: 42)
            $table->string('nom'); // Colonne pour le nom (ex: "Kasserine")
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gouvernorats');
    }
};