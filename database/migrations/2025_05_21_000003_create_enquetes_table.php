<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquetes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('statut');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquetes');
    }
};