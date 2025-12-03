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
       // Table Formateurs
        Schema::create('formateurs', function (Blueprint $table) {
            $table->id();
            $table->string('mle')->unique(); // Matricule
            $table->string('nom_complet');
            $table->string('type')->default('permanent'); // permanent, vacataire
            $table->decimal('masse_horaire', 8, 2)->default(910.00) ;// Masse horaire annuelle
            $table->string('description')->default('Aucun description');

            $table->timestamps();
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formateurs');
    }
};
