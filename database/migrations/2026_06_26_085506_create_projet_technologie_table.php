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
        Schema::create('projet_technologie', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projet_id');
            $table->unsignedBigInteger('technologie_id');
            $table->timestamps();

            $table->foreign('projet_id')->references('id')->on('projets')->onDelete('cascade');
            $table->foreign('technologie_id')->references('id')->on('technologies')->onDelete('cascade');
            $table->unique(['projet_id', 'technologie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet_technologie');
    }
};
