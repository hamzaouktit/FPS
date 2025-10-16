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
        // Table Affectations
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_id')->constrained('groupes')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->string('code_efp')->nullable();
             $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            // Formateurs affectés
            $table->string('mle_affecte_presentiel')->nullable();
            $table->foreign('mle_affecte_presentiel')
                  ->references('mle')
                  ->on('formateurs')
                  ->onDelete('set null');
            $table->string('formateur_affecte_presentiel')->nullable();
            $table->string('mle_affecte_syn')->nullable();
            $table->foreign('mle_affecte_syn')
                  ->references('mle')
                  ->on('formateurs')
                  ->onDelete('set null');
            $table->string('formateur_affecte_syn')->nullable();
            
            // Masses horaires Semestre 1 DRIF
            $table->decimal('mhp_s1_drif', 8, 2)->default(0);
            $table->decimal('mhsyn_s1_drif', 8, 2)->default(0);
            $table->decimal('mhasyn_s1_drif', 8, 2)->default(0);
            $table->decimal('mh_totale_s1_drif', 8, 2)->default(0);
            
            // Masses horaires Semestre 2 DRIF
            $table->decimal('mhp_s2_drif', 8, 2)->default(0);
            $table->decimal('mhsyn_s2_drif', 8, 2)->default(0);
            $table->decimal('mhasyn_s2_drif', 8, 2)->default(0);
            $table->decimal('mh_totale_s2_drif', 8, 2)->default(0);
            
            // Masses horaires Totales DRIF
            $table->decimal('mhp_totale_drif', 8, 2)->default(0);
            $table->decimal('mhsyn_totale_drif', 8, 2)->default(0);
            $table->decimal('mhasyn_totale_drif', 8, 2)->default(0);
            $table->decimal('mh_totale_drif', 8, 2)->default(0);
            
            // Masses horaires Affectées
            $table->decimal('mh_affectee_presentiel', 8, 2)->default(0);
            $table->decimal('mh_affectee_sync', 8, 2)->default(0);
            $table->decimal('mh_affectee_globale', 8, 2)->default(0);
            
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['groupe_id', 'module_id']);
            $table->index('mle_affecte_presentiel');
            $table->index('mle_affecte_syn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
