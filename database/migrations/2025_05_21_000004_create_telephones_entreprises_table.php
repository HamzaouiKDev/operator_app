<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telephones_entreprises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->string('numero');
            $table->string('source')->nullable();
            $table->boolean('est_primaire')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telephones_entreprises');
    }
};