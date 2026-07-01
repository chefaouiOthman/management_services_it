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

            // 1. AJOUT des colonnes indispensables demandées par le contrôleur
            $table->string('marque', 100);
            $table->string('modele', 100);

            // 2. CORRECTION de 'assigne' en 'attribue' pour s'aligner avec le contrôleur
            $table->enum('statut_materiel', ['disponible', 'attribue', 'en_panne', 'reforme']);

            // 3. AJOUT de ->nullable() pour éviter que la base de données bloque
            // tant qu'on n'a pas ajouté ces champs dans le formulaire
            $table->date('date_achat_actif')->nullable();
            $table->decimal('prix_achat', 10, 2)->nullable();

            $table->timestamps();

            // Clé étrangère
            $table->foreign('type_materiel_id')->references('id')->on('type_materials')->onDelete('cascade');
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
