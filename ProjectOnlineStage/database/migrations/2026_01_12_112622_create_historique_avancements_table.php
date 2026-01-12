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
        Schema::create('historique_avancements', function (Blueprint $table) {
            $table->id();
            
            // Date de capture de l'historique
            $table->date('date_capture')->index();
            
            // Relations
            $table->foreignId('affectation_id')->constrained('affectations')->onDelete('cascade');
            $table->string('code_efp')->nullable();
            $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            
            // Informations contextuelles (dénormalisées pour consultation rapide)
            $table->string('formateur_presentiel')->nullable();
            $table->string('mle_presentiel')->nullable();
            $table->string('formateur_syn')->nullable();
            $table->string('mle_syn')->nullable();
            $table->string('nom_module')->nullable();
            $table->string('code_module')->nullable();
            $table->string('code_groupe')->nullable();
            $table->string('nom_filiere')->nullable();
            
            // Masses horaires affectées (pour référence)
            $table->decimal('mh_affectee_presentiel', 8, 2)->default(0);
            $table->decimal('mh_affectee_sync', 8, 2)->default(0);
            $table->decimal('mh_affectee_globale', 8, 2)->default(0);
            
            // Masses horaires réalisées (données d'avancement)
            $table->decimal('mh_realisee_presentiel', 8, 2)->default(0);
            $table->decimal('mh_realisee_sync', 8, 2)->default(0);
            $table->decimal('mh_realisee_globale', 8, 2)->default(0);
            
            // Taux de réalisation
            $table->decimal('taux_realisation_presentiel', 5, 2)->default(0);
            $table->decimal('taux_realisation_syn', 5, 2)->default(0);
            $table->decimal('taux_realisation_globale', 5, 2)->default(0);
            
            // Statistiques
            $table->decimal('moyenne_absence', 5, 2)->default(0);
            $table->integer('nb_cc')->default(0);
            $table->string('seance_efm')->default('Non');
            $table->string('validation_efm')->default('non');
            $table->string('classe_teams')->nullable();
            
            $table->timestamps();
            
            // Index composites pour optimiser les requêtes
            $table->index(['code_efp', 'date_capture']);
            $table->index(['date_capture', 'affectation_id']);
            $table->index(['mle_presentiel', 'date_capture']);
            $table->index(['mle_syn', 'date_capture']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_avancements');
    }
};