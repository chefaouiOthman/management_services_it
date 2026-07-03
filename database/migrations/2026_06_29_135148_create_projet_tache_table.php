<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md : Composite PK (projet_id, tache_id) + attributs de pivot priorite et statut_tache.
     */
    public function up(): void
    {
        Schema::create('projet_tache', function (Blueprint $table) {
            // Clés étrangères composant la PK composite
            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('tache_id');

            // Attributs de pivot définis dans GEMINI.md
            $table->enum('priorite', ['basse', 'moyenne', 'haute', 'bloquante'])->default('moyenne');
            $table->enum('statut_tache', ['backlog', 'en_cours', 'en_revue', 'termine'])->default('backlog');

            $table->timestamps();

            // Clé primaire composite (conformes GEMINI.md)
            $table->primary(['projet_id', 'tache_id']);

            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_tache');
    }
};
