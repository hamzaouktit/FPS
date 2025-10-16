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
        // Table de liaison Filiere-Module (un module peut appartenir à plusieurs filières)
        Schema::create('filiere_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->timestamps();
            
            // Index unique pour éviter les doublons
            $table->unique(['filiere_id', 'module_id']);
            
            // Index pour optimiser les requêtes
            $table->index('filiere_id');
            $table->index('module_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filiere_module');
    }
};