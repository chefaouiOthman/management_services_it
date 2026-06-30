<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feuille_temps_tache', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feuille_temps_id');
            $table->unsignedBigInteger('tache_id');
            $table->timestamps();

            $table->foreign('feuille_temps_id')->references('id')->on('feuille_temps')->onDelete('cascade');
            $table->foreign('tache_id')->references('id')->on('taches')->onDelete('cascade');
            $table->unique(['feuille_temps_id', 'tache_id'], 'ft_tache_unique');
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
