<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->string('code_filiere', 20)->primary(); // PK string
            $table->string('nom_filiere', 100)->nullable(false);
            $table->string('nom_secteur', 50);
            $table->foreign('nom_secteur')->references('nom_secteur')->on('secteurs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filieres');
    }
};