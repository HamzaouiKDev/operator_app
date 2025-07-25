<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('echantillons_enquetes', function (Blueprint $table) {
            if (!Schema::hasColumn('echantillons_enquetes', 'entreprise_id')) {
                $table->foreignId('entreprise_id')->constrained('entreprises')->onDelete('cascade')->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('echantillons_enquetes', function (Blueprint $table) {
            $table->dropForeign(['entreprise_id']);
            $table->dropColumn('entreprise_id');
        });
    }
};
?>