<?php
// database/migrations/xxxx_add_tracking_fields_to_echantillons_enquetes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingFieldsToEchantillonsEnquetesTable extends Migration
{
    public function up()
    {
        Schema::table('echantillons_enquetes', function (Blueprint $table) {
            // Ajouter les champs de traçabilité s'ils n'existent pas
            if (!Schema::hasColumn('echantillons_enquetes', 'date_attribution')) {
                $table->timestamp('date_attribution')->nullable()->after('utilisateur_id');
            }
            if (!Schema::hasColumn('echantillons_enquetes', 'date_mise_a_jour')) {
                $table->timestamp('date_mise_a_jour')->nullable()->after('date_attribution');
            }
            if (!Schema::hasColumn('echantillons_enquetes', 'date_liberation')) {
                $table->timestamp('date_liberation')->nullable()->after('date_mise_a_jour');
            }

            // Ajouter des index pour performance
            $table->index(['utilisateur_id', 'statut']);
            $table->index(['statut']);
            $table->index(['priorite']);
        });
    }

    public function down()
    {
        Schema::table('echantillons_enquetes', function (Blueprint $table) {
            $table->dropColumn(['date_attribution', 'date_mise_a_jour', 'date_liberation']);
            $table->dropIndex(['utilisateur_id', 'statut']);
            $table->dropIndex(['statut']);
            $table->dropIndex(['priorite']);
        });
    }
}
