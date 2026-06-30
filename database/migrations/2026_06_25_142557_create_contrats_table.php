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
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->enum('type_contrat', ['CDI', 'CDD', 'Stage', 'Freelance']);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('salaire_base', 10, 2);
            $table->integer('heures_hebdo');
            $table->enum('statut', ['actif', 'suspendu', 'termine']);
            $table->timestamps();

            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
