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
        Schema::create('session_formations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalogue_formation_id');
            $table->unsignedBigInteger('employe_id');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('salle_virtuelle', 255)->nullable();
            $table->string('salle_concrete', 255)->nullable();
            $table->timestamps();

            $table->foreign('catalogue_formation_id')->references('id')->on('catalogue_formations')->onDelete('cascade');
            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
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
