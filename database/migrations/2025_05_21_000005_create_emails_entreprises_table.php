<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emails_entreprises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade');
            $table->string('email');
            $table->string('source')->nullable();
            $table->boolean('est_primaire')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emails_entreprises');
    }
};