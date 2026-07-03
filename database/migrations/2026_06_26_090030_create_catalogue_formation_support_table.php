<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md pivot #5 : CatalogueFormation ↔ SupportCours.
     * Doit être créée APRÈS catalogue_formations (085945) et support_cours (085950).
     */
    public function up(): void
    {
        Schema::create('catalogue_formation_support', function (Blueprint $table) {
            $table->unsignedBigInteger('catalogue_formation_id');
            $table->unsignedBigInteger('support_cours_id');

            // Clé primaire composite
            $table->primary(['catalogue_formation_id', 'support_cours_id']);

            $table->foreign('catalogue_formation_id')
                  ->references('id')
                  ->on('catalogue_formations')
                  ->onDelete('cascade');

            $table->foreign('support_cours_id')
                  ->references('id')
                  ->on('support_cours')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogue_formation_support');
    }
};
