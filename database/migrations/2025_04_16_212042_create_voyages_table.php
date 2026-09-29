<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('voyages', function (Blueprint $table) {
            $table->id();
            $table->string('ville_depart');
            $table->string('ville_arrivee');
            $table->dateTime('date_depart');
            $table->decimal('prix', 10, 2);
            // Ce champ représente le nombre de places encore disponibles.
            $table->unsignedInteger('nombre_places');
            $table->string('classe', 20)->default('classique');
            $table->foreignId('agent_id')->constrained('utilisateurs')->restrictOnDelete();
            $table->timestamps();

            $table->index(['ville_depart', 'ville_arrivee', 'date_depart']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voyages');
    }
};
