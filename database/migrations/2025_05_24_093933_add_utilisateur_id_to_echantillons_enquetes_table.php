<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('echantillons_enquetes', function (Blueprint $table) {
            $table->bigInteger('utilisateur_id')->unsigned()->nullable()->after('priorite');
            $table->foreign('utilisateur_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('echantillons_enquetes', function (Blueprint $table) {
            $table->dropForeign(['utilisateur_id']);
            $table->dropColumn('utilisateur_id');
        });
    }
};
