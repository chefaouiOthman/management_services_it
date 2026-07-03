<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md : SupportCours est une entité standalone.
     * Le lien CatalogueFormation ↔ SupportCours passe par le pivot catalogue_formation_support.
     */
    public function up(): void
    {
        Schema::create('support_cours', function (Blueprint $table) {
            $table->id();
            $table->string('nom_fichier', 150);
            $table->string('url_stockage', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_cours');
    }
};
