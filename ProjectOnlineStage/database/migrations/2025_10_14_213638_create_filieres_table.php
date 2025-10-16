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
        // Table Filieres
        Schema::create('filieres', function (Blueprint $table) {
            $table->id();
            $table->string('code_filiere');
            $table->string('nom_filiere');
            $table->foreignId('secteur_id')->constrained('secteurs')->onDelete('cascade');
            $table->string('code_efp')->nullable();
             $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            $table->timestamps();
            $table->index('code_filiere');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filieres');
    }
};
