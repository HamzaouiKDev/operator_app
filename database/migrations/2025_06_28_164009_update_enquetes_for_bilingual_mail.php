<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquetes', function (Blueprint $table) {
            // Renommer les anciennes colonnes en version arabe
            $table->renameColumn('titre_mail', 'titre_mail_ar');
            $table->renameColumn('corps_mail', 'corps_mail_ar');

            // Ajouter les nouvelles colonnes pour la version française
            $table->string('titre_mail_fr')->nullable()->after('titre_mail_ar');
            $table->text('corps_mail_fr')->nullable()->after('corps_mail_ar');
        });
    }

    public function down(): void
    {
        Schema::table('enquetes', function (Blueprint $table) {
            // Inverser les opérations en cas de rollback
            $table->renameColumn('titre_mail_ar', 'titre_mail');
            $table->renameColumn('corps_mail_ar', 'corps_mail');
            $table->dropColumn(['titre_mail_fr', 'corps_mail_fr']);
        });
    }
};