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
        Schema::create('employes', function (Blueprint $table) {
            // Clé primaire et héritage d'identité avec la table 'users'
            $table->unsignedBigInteger('user_id')->primary();

            // Attributs propres de l'employé
            $table->date('date_embauche');
            $table->string('CIN', 50)->unique();

            // Association : Un employé appartient à un département (Clé Étrangère)
            $table->foreignId('departement_id')->constrained('departements')->onDelete('restrict');

            $table->timestamps();

            // Déclaration de la contrainte d'héritage d'identité
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
