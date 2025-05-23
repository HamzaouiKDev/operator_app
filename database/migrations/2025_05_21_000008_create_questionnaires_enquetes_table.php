<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaires_enquetes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enquete_id')->constrained('enquetes')->onDelete('cascade');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questionnaires_enquetes');
    }
};