<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->string('code_filiere');
            $table->string('nom_filiere');
            $table->unsignedBigInteger('secteur_id');
            $table->string('code_efp');
            $table->foreign('secteur_id')->references('id')->on('secteurs')->onDelete('cascade');
            $table->foreign('code_efp')->references('code_efp')->on('etablissements')->onDelete('cascade');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('filieres');
    }
};