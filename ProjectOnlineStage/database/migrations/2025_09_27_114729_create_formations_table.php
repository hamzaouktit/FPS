<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id(); // PK auto-incrément
            $table->integer('annee')->nullable(false);
            $table->string('code_efp');
            $table->string('niveau');
            $table->string('code_filiere');
            $table->string('type_formation')->nullable();
            $table->string('creneau')->nullable();
            $table->foreign('code_efp')->references('code_efp')->on('etablissements')->onDelete('cascade');
            $table->foreign('niveau')->references('niveau')->on('niveaux')->onDelete('cascade');
            $table->foreign('code_filiere')->references('code_filiere')->on('filieres')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};