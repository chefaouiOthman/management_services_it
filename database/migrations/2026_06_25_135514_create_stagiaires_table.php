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
        Schema::create('stagiaires', function (Blueprint $table) {
            // Clé primaire et héritage avec la table 'users'
            $table->unsignedBigInteger('id')->primary();

            // Attributs propres au stagiaire
            $table->string('ecole_origine', 150);
            $table->text('sujet_stage');

            // Association : Un stagiaire est encadré obligatoirement par un employé (Clé Étrangère)
            $table->foreignId('employe_id')->constrained('employes')->onDelete('restrict');

            $table->timestamps();

            // Déclaration de la contrainte d'héritage d'identité
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};
