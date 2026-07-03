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
            // Clé primaire et héritage d'identité (pattern One-to-One identitaire)
            // user_id est à la fois PK et FK vers users.id — PAS d'auto-increment
            $table->unsignedBigInteger('user_id')->primary();

            // Attributs propres au stagiaire (conformes GEMINI.md)
            $table->string('ecole_origine', 150);
            $table->text('sujet_stage');

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
        Schema::dropIfExists('stagiaires');
    }
};
