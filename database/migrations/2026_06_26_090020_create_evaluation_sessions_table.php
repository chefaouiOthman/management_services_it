<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md :
     *   - user_id    = l'apprenant (Student) qui soumet l'évaluation
     *   - employe_id = le formateur (Trainer) qui est évalué
     *   - avis_textuel est Nullable
     */
    public function up(): void
    {
        Schema::create('evaluation_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_formation_id');
            $table->unsignedBigInteger('user_id');     // Apprenant (Student)
            $table->unsignedBigInteger('employe_id');  // Formateur évalué (Trainer)
            $table->integer('note_pedagogie');
            $table->integer('note_technique');
            $table->text('avis_textuel')->nullable();  // Nullable selon GEMINI.md
            $table->timestamps();

            $table->foreign('session_formation_id')
                  ->references('id')
                  ->on('session_formations')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            // employe_id FK → employes.user_id (PK identitaire)
            $table->foreign('employe_id')
                  ->references('user_id')
                  ->on('employes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_sessions');
    }
};
