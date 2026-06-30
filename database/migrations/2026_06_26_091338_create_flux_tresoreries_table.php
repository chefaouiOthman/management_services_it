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
        Schema::create('flux_tresoreries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categorie_flux_id');

            $table->unsignedBigInteger('facture_id')->nullable();
            $table->unsignedBigInteger('fiche_paie_id')->nullable();
            $table->unsignedBigInteger('note_de_frais_id')->nullable();

            $table->enum('type_mouvement', ['entree', 'sortie']);
            $table->decimal('montant_operation', 12, 2);
            $table->dateTime('date_comptable');
            $table->timestamps();

            // Clés étrangères
            $table->foreign('categorie_flux_id')->references('id')->on('categorie_flux')->onDelete('cascade');
            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('set null');
            $table->foreign('fiche_paie_id')->references('id')->on('fiche_paies')->onDelete('set null');
            $table->foreign('note_de_frais_id')->references('id')->on('note_de_frais')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flux_tresoreries');
    }
};
