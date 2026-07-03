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
            $table->unsignedBigInteger('flux_tresorerie_id')->nullable();
            $table->string('motif_depense', 255);
            $table->decimal('montant_ttc', 10, 2);
            $table->string('justificatif_path', 255);
            $table->enum('statut_remboursement', ['soumis', 'approuve_manager', 'rejete', 'rembourse']);
            $table->timestamps();

            $table->foreign('employe_id')->references('user_id')->on('employes')->onDelete('cascade');
            $table->foreign('flux_tresorerie_id')->references('id')->on('flux_tresoreries')->onDelete('set null');
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
