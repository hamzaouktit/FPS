<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_groupe');
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->integer('effectif_groupe')->nullable();
            $table->string('sous_groupe')->nullable();
            $table->string('statut_sous_groupe')->nullable();
            $table->string('fusion_groupe')->nullable();
            $table->string('code_fusion')->nullable();
            $table->integer('annee_formation')->nullable();
            $table->string('code_efp');
            $table->foreign('code_efp')->references('code_efp')->on('etablissements')->onDelete('cascade');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};