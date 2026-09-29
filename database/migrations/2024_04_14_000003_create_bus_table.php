<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bus', function (Blueprint $table) {
            $table->id();
            $table->string('numero_bus')->unique();
            $table->integer('capacite');              //  nouveau champ
            $table->string('chauffeur');              //  nouveau champ
            $table->string('type')->nullable();       // optionnel, selon ton besoin
            $table->timestamps();
        });
        
    }

    public function down(): void {
        Schema::dropIfExists('bus');
    }
};
