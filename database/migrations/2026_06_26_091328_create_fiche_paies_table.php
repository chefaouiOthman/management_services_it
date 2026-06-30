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
        Schema::create('fiche_paies', function (Blueprint $table) {
            $table->id(); // Id_fiche_paie [cite: 23]
            $table->unsignedBigInteger('employe_id');
            $table->string('mois_annee', 7);
            $table->decimal('net_a_payer', 10, 2);
            $table->timestamps();

            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiche_paies');
    }
};
