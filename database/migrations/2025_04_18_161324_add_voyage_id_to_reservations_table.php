<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Ajoute la colonne voyage_id à la table reservations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('voyage_id')->after('utilisateur_id');

            // Clé étrangère vers la table voyages
            $table->foreign('voyage_id')
                  ->references('id')
                  ->on('voyages')
                  ->onDelete('restrict');
        });
    }

    /**
     * Supprime la colonne voyage_id si on rollback.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['voyage_id']);
            $table->dropColumn('voyage_id');
        });
    }
};
