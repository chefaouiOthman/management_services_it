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
            // Clé primaire et héritage d'identité (pattern One-to-One identitaire)
            // user_id est à la fois PK et FK vers users.id — PAS d'auto-increment
            $table->unsignedBigInteger('user_id')->primary();

            // Attributs propres de l'employé (conformes GEMINI.md)
            $table->date('date_embauche');
            $table->string('CIN', 50)->unique();

            $table->timestamps();

            // Contrainte d'héritage : cascade si l'utilisateur est supprimé
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
