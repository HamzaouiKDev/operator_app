<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emails_entreprises', function (Blueprint $table) {
            // On ajoute une colonne pour la date d'utilisation, qui peut être nulle.
            $table->timestamp('last_used_at')->nullable()->after('est_primaire');
        });
    }

    public function down(): void
    {
        Schema::table('emails_entreprises', function (Blueprint $table) {
            $table->dropColumn('last_used_at');
        });
    }
};