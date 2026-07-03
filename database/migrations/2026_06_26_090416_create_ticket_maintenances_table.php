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
        Schema::create('ticket_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_materiel_id');
            $table->unsignedBigInteger('user_id');
            $table->text('description_panne');
            $table->decimal('cout_reparation', 10, 2);
            $table->enum('statut_ticket', ['signale', 'en_atelier', 'resolu']);
            $table->timestamps();

            $table->foreign('asset_materiel_id')->references('id')->on('asset_materiels')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_maintenances');
    }
};
