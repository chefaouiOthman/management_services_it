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
        Schema::create('feuille_temps', function (Blueprint $table) {
            $table->id(); // Id_feuille
            $table->unsignedBigInteger('employe_id');
            $table->unsignedBigInteger('projet_id');
            $table->date('date_effort');
            $table->decimal('duree_heures', 4, 2);
            $table->text('commentaire');
            $table->timestamps();

            // employe_id FK → employes.user_id (PK identitaire, pas employes.id)
            $table->foreign('employe_id')->references('user_id')->on('employes')->onDelete('cascade');
            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feuille_temps');
    }
};
