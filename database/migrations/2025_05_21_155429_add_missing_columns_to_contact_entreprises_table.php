<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_entreprises', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_entreprises', 'civilite')) {
                $table->string('civilite')->nullable()->after('entreprise_id');
            }
            if (!Schema::hasColumn('contact_entreprises', 'prenom')) {
                $table->string('prenom')->after('civilite');
            }
            if (!Schema::hasColumn('contact_entreprises', 'nom')) {
                $table->string('nom')->after('prenom');
            }
            if (!Schema::hasColumn('contact_entreprises', 'email')) {
                $table->string('email')->nullable()->after('nom');
            }
            if (!Schema::hasColumn('contact_entreprises', 'telephone')) {
                $table->string('telephone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('contact_entreprises', 'poste')) {
                $table->string('poste')->nullable()->after('telephone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contact_entreprises', function (Blueprint $table) {
            $table->dropColumn(['civilite', 'prenom', 'nom', 'email', 'telephone', 'poste']);
        });
    }
};