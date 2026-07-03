<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * GEMINI.md : SessionFormation N'a PAS de employe_id direct.
     * Le lien Formateur ↔ Session se fait via le pivot employe_session_formation.
     */
    public function up(): void
    {
        Schema::create('session_formations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalogue_formation_id');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('salle_virtuelle', 255)->nullable();
            $table->string('salle_concrete', 255)->nullable();
            $table->timestamps();

            $table->foreign('catalogue_formation_id')
                  ->references('id')
                  ->on('catalogue_formations')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_formations');
    }
};
