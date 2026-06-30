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
        Schema::create('support_cours', function (Blueprint $table) {
            $table->id(); // Id_support_cours
            $table->unsignedBigInteger('catalogue_formation_id');
            $table->string('nom_fichier', 150);
            $table->string('url_stockage', 255);
            $table->timestamps();

            $table->foreign('catalogue_formation_id')->references('id')->on('catalogue_formations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_cours');
    }
};
