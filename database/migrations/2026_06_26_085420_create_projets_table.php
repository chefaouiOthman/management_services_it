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
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->string('nom_projet', 150);
            $table->text('description');
            $table->decimal('budget_vendu', 12, 2);
            $table->enum('statut_projet', ['analyse', 'developpement', 'recette', 'deploie', 'maintenance']);
            $table->timestamps();

            // client_id FK → clients.user_id (PK identitaire, pas clients.id)
            $table->foreign('client_id')->references('user_id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
