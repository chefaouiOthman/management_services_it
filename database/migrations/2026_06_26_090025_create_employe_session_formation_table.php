<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md pivot #4 : Employe (Formateur) ↔ SessionFormation.
     * Doit être créée APRÈS session_formations (090007) et employes (135501).
     */
    public function up(): void
    {
        Schema::create('employe_session_formation', function (Blueprint $table) {
            // employe_id FK → employes.user_id (PK identitaire)
            $table->unsignedBigInteger('employe_id');
            $table->unsignedBigInteger('session_formation_id');

            // Clé primaire composite
            $table->primary(['employe_id', 'session_formation_id']);

            $table->foreign('employe_id')
                  ->references('user_id')
                  ->on('employes')
                  ->onDelete('cascade');

            $table->foreign('session_formation_id')
                  ->references('id')
                  ->on('session_formations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employe_session_formation');
    }
};
