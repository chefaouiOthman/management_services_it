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
        Schema::create('livrables', function (Blueprint $table) {
            $table->id(); // Id_livrable
            $table->unsignedBigInteger('projet_id');
            $table->string('titre_jalon', 150);
            $table->date('date_limite_soumission');
            $table->enum('statut_client', ['en_attente', 'rejete_avec_corrections', 'valide']);
            $table->timestamps();

            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livrables');
    }
};
