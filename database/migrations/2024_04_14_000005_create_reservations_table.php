<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('utilisateur_id');
            $table->date('date_voyage');
            $table->string('ville_depart');
            $table->string('destination');
            $table->string('classe');
            $table->integer('nb_places');
            $table->string('statut_paiement')->default('en_attente');
            $table->timestamp('date_reservation')->useCurrent();
            $table->timestamps();

            $table->foreign('utilisateur_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('reservations');
    }
};
