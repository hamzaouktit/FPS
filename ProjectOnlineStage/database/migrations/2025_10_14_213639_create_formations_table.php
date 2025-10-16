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
       // Table Formations
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->integer('annee');
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');
            $table->string('type'); // Diplômante, Qualifiante, PP
            $table->string('mode'); // Résidentiel, Alterné
            $table->string('creneau'); // CDJ, CDS
            $table->string('code_efp')->nullable();
             $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            $table->timestamps();
            $table->index('annee');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
