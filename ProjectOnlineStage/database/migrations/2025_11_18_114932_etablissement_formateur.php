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
        // Table pivot pour la relation Many-to-Many entre Etablissement et Formateur
        Schema::create('etablissement_formateur', function (Blueprint $table) {
            $table->id();
            $table->string('code_efp');
            $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('cascade');
            
            $table->foreignId('formateur_id')
                  ->constrained('formateurs')
                  ->onDelete('cascade');
            
            $table->timestamps();
            
            // Index unique pour éviter les doublons
            $table->unique(['code_efp', 'formateur_id']);
            
            // Index pour optimiser les requêtes
            $table->index('code_efp');
            $table->index('formateur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etablissement_formateur');
    }
};