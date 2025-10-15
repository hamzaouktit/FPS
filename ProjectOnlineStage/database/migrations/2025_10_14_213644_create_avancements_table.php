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
        // Table Avancements
        Schema::create('avancements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affectation_id')->constrained('affectations')->onDelete('cascade');
            
            // Masses horaires réalisées
            $table->decimal('mh_realisee_presentiel', 8, 2)->default(0);
            $table->decimal('mh_realisee_sync', 8, 2)->default(0);
            $table->decimal('mh_realisee_globale', 8, 2)->default(0);
            
            // Taux de réalisation
            $table->decimal('taux_realisation_presentiel', 5, 2)->default(0);
            $table->decimal('taux_realisation_syn', 5, 2)->default(0);
            $table->decimal('taux_realisation_globale', 5, 2)->default(0);
            
            // Statistiques
            $table->decimal('moyenne_absence', 5, 2)->default(0);
            $table->integer('nb_cc')->default(0); // Nombre de contrôles continus
            $table->string('seance_efm')->default('Non'); // Oui/Non
            $table->string('validation_efm')->default('non'); // oui/non
            $table->string('classe_teams')->nullable();
            
            $table->date('date_maj')->nullable();
            $table->string('code_efp')->nullable();
             $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            $table->timestamps();
            
            $table->index('affectation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avancements');
    }
};
