<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->integer('annee')->nullable(false);
            $table->string('code_efp');
            $table->unsignedBigInteger('niveau_id');
            $table->unsignedBigInteger('filiere_id');
            $table->string('type_formation')->nullable();
            $table->string('creneau')->nullable();
            $table->foreign('code_efp')->references('code_efp')->on('etablissements')->onDelete('cascade');
            $table->foreign('niveau_id')->references('id')->on('niveaux')->onDelete('cascade');
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('cascade');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};