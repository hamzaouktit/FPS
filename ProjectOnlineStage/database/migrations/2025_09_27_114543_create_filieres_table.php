<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->string('code_filiere')->primary(); // PK string
            $table->string('nom_filiere')->nullable(false);
            $table->string('nom_secteur');
            $table->string('code_efp');
            $table->foreign('nom_secteur')->references('nom_secteur')->on('secteurs')->onDelete('cascade');
            $table->foreign('code_efp')->references('code_efp')->on('etablissements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filieres');
    }
};