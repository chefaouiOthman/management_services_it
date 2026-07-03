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
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('flux_tresorerie_id')->nullable();
            $table->string('num_facture', 50)->unique();
            $table->date('date_emission'); //
            $table->enum('statut_paiement', ['emise', 'en_retard_paiement', 'soldee']);
            $table->timestamps();

            $table->foreign('client_id')->references('user_id')->on('clients')->onDelete('cascade');
            $table->foreign('flux_tresorerie_id')->references('id')->on('flux_tresoreries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
