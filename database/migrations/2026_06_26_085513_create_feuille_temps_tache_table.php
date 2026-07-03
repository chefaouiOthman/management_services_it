<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md : Composite PK (feuille_temps_id, tache_id). Pas d'attributs de pivot supplémentaires.
     */
    public function up(): void
    {
        Schema::create('feuille_temps_tache', function (Blueprint $table) {
            // Clés étrangères composant la PK composite
            $table->unsignedBigInteger('feuille_temps_id');
            $table->unsignedBigInteger('tache_id');

            // Clé primaire composite (conforme GEMINI.md)
            $table->primary(['feuille_temps_id', 'tache_id']);

            $table->foreign('feuille_temps_id')->references('id')->on('feuille_temps')->onDelete('cascade');
            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feuille_temps_tache');
    }
};
