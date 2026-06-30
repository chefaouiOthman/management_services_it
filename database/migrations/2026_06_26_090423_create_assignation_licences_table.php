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
        Schema::create('assignation_licences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employe_id');
            $table->unsignedBigInteger('licence_logiciel_id');
            $table->date('date_attribution');
            $table->date('date_revocation')->nullable();
            $table->timestamps();

            $table->foreign('employe_id')->references('id')->on('employes')->onDelete('cascade');
            $table->foreign('licence_logiciel_id')->references('id')->on('licence_logiciels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignation_licences');
    }
};
