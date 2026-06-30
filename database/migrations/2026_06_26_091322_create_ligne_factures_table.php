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
        Schema::create('ligne_factures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('facture_id');
            $table->string('designation', 255);
            $table->decimal('quantite', 10, 2);
            $table->decimal('prix_unitaire_ht', 10, 2);
            $table->decimal('taux_tva', 4, 2);
            $table->timestamps();

            $table->foreign('facture_id')->references('id')->on('factures')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_factures');
    }
};
