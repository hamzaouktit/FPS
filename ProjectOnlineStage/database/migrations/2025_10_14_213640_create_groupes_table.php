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
        // Table Groupes
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->string('code_groupe');
            $table->integer('effectif_groupe')->default(0);
            $table->string('statut')->default('Actif'); // Actif, Inactif
            $table->string('sous_groupe'); 
            $table->string('statut_sous_groupe')->default('Actif'); // Actif, Inactif
            $table->string('fusion_groupe')->nullable();
            $table->string('code_fusion')->nullable();
            $table->integer('annee_formation');
            $table->foreignId('filiere_id')->constrained('filieres')->onDelete('cascade');
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->string('code_efp')->nullable();
             $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            $table->timestamps();
            // Index
            $table->index('code_groupe');
            $table->index('statut');
            $table->index('annee_formation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};
