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
        Schema::create('note_de_frais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->string('motif_depense', 255);
            $table->decimal('montant_ttc', 10, 2);
            $table->string('justificatif_path', 255);
            $table->enum('statut_remboursement', ['soumis', 'approuve_manager', 'rejete', 'rembourse']);
            $table->timestamps();

            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_de_frais');
    }
};
