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
        Schema::create('evaluation_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_formation_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('note_pedagogie');
            $table->integer('note_technique');
            $table->text('avis_textuel');
            $table->timestamps();

            $table->foreign('session_formation_id')->references('id')->on('session_formations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['session_formation_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_sessions');
    }
};
