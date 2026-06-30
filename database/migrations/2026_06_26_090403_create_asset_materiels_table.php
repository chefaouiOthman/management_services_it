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
        Schema::create('asset_materiels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_materiel_id');
            $table->string('num_serie', 100)->unique();
            $table->date('date_achat_actif');
            $table->enum('statut_materiel', ['disponible', 'assigne', 'en_panne', 'reforme']);
            $table->decimal('prix_achat', 10, 2);
            $table->timestamps();

            $table->foreign('type_materiel_id')->references('id')->on('type_materiels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_materiels');
    }
};
