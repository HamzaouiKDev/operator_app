<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactIdToTelephoneEntreprisesTable extends Migration
{
    public function up()
{
    Schema::table('telephones_entreprises', function (Blueprint $table) {
        $table->unsignedBigInteger('contact_id')->nullable()->after('entreprise_id');

        // La clé étrangère est définie ici, mais sans l'action en cascade
        $table->foreign('contact_id')
              ->references('id')
              ->on('contact_entreprises');
    });
}

    public function down()
    {
        Schema::table('telephones_entreprises', function (Blueprint $table) {
            // Important : Vérifiez le nom de la contrainte si elle a été nommée automatiquement
            // Vous pouvez le trouver dans votre SGBD ou le nommer explicitement dans la méthode up()
            // ex: $table->dropForeign(['contact_id']); ou $table->dropForeign('telephone_entreprises_contact_id_foreign');
            // Pour plus de sûreté, il est bon de nommer la contrainte dans up():
            // $table->foreign('contact_id', 'fk_tel_entreprise_contact')->references('id')->on('contacts');
            // puis dans down(): $table->dropForeign('fk_tel_entreprise_contact');

            // Solution plus simple si vous n'avez pas nommé la contrainte :
            if (Schema::hasColumn('telephones_entreprises', 'contact_id')) {
                 // D'abord supprimer la clé étrangère si elle existe
                if (DB::getDriverName() !== 'sqlite') { // SQLite gère différemment les dropForeign
                    // Pour MySQL, PostgreSQL, etc., on peut avoir besoin de connaître le nom de la contrainte
                    // Laravel > 8.x tente de le deviner, mais pour les anciennes versions ou des cas complexes :
                    // $table->dropForeign(['contact_id']); // Laravel essaiera de deviner le nom
                    // Si cela échoue, vous devrez spécifier le nom de la contrainte exact.
                    // Vous pouvez inspecter votre base de données pour trouver ce nom.
                    // Par exemple: $table->dropForeign('telephone_entreprises_contact_id_foreign');
                }
                 $table->dropColumn('contact_id');
            }
        });
    }
}