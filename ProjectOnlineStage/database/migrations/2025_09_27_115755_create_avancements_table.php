<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avancements', function (Blueprint $table) {
            $table->id(); // PK auto-incrément
            $table->timestamp('date_maj')->nullable();
            $table->string('groupe', 20);
            $table->string('code_module', 20);
            $table->string('mode', 20)->nullable();
            $table->string('mle_presentiel', 20)->nullable();
            $table->string('mle_syn', 20)->nullable();
            // Heures S1
            $table->decimal('mhp_s1_drif', 5, 2)->default(0);
            $table->decimal('mhsyn_s1_drif', 5, 2)->default(0);
            $table->decimal('mhasyn_s1_drif', 5, 2)->default(0);
            $table->decimal('mh_totale_s1_drif', 5, 2)->default(0);
            // Heures S2
            $table->decimal('mhp_s2_drif', 5, 2)->default(0);
            $table->decimal('mhsyn_s2_drif', 5, 2)->default(0);
            $table->decimal('mhasyn_s2_drif', 5, 2)->default(0);
            $table->decimal('mh_totale_s2_drif', 5, 2)->default(0);
            // Totaux DRIF
            $table->decimal('mhp_totale_drif', 5, 2)->default(0);
            $table->decimal('mhsyn_totale_drif', 5, 2)->default(0);
            $table->decimal('mhasyn_totale_drif', 5, 2)->default(0);
            $table->decimal('mh_totale_drif', 5, 2)->default(0);
            // Affectées et réalisées
            $table->decimal('mh_affectee_presentiel', 5, 2)->default(0);
            $table->decimal('mh_affectee_sync', 5, 2)->default(0);
            $table->decimal('mh_affectee_globale', 5, 2)->default(0);
            $table->decimal('mh_realisee_presentiel', 5, 2)->default(0);
            $table->decimal('mh_realisee_sync', 5, 2)->default(0);
            $table->decimal('mh_realisee_globale', 5, 2)->default(0);
            // Taux et autres
            $table->decimal('taux_realisation_presentiel', 5, 2)->default(0);
            $table->decimal('taux_realisation_syn', 5, 2)->default(0);
            $table->decimal('taux_realisation_global', 5, 2)->default(0);
            $table->decimal('moy_absence', 5, 2)->default(0);
            $table->integer('nb_cc')->default(0);
            $table->string('seance_efm', 10)->nullable();
            $table->string('validation_efm', 10)->nullable();
            $table->string('classe_teams', 50)->nullable();
            $table->string('module_pie', 50)->nullable();
            $table->string('efp_pie', 50)->nullable();
            // Relations FK
            $table->foreign('groupe')->references('groupe')->on('groupes')->onDelete('cascade');
            $table->foreign('code_module')->references('code_module')->on('modules')->onDelete('cascade');
            $table->foreign('mle_presentiel')->references('mle')->on('formateurs')->onDelete('set null');
            $table->foreign('mle_syn')->references('mle')->on('formateurs')->onDelete('set null');
            $table->softDeletes(); // Pour gérer les suppressions logiques
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avancements');
    }
};