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
            $table->unsignedBigInteger('user_id')->primary();

            // Attributs propres au stagiaire
            $table->string('ecole_origine', 150);
            $table->text('sujet_stage');

            // Association : Un stagiaire est encadré obligatoirement par un employé (Clé Étrangère)
            $table->unsignedBigInteger('employe_id');

            $table->timestamps();

            // Déclaration de la contrainte d'héritage d'identité
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('employe_id')->references('user_id')->on('employes')->onDelete('restrict');
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
