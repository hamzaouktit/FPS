<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groupes', function (Blueprint $table) {
            $table->string('groupe')->primary(); // PK string (composite si besoin avec id_formation)
            $table->foreignId('id_formation')->constrained('formations')->onDelete('cascade');
            $table->integer('effectif_groupe')->nullable();
            $table->string('sous_groupe')->nullable();
            $table->string('statut_sous_groupe')->nullable();
            $table->string('fusion_groupe')->nullable();
            $table->string('code_fusion')->nullable();
            $table->integer('annee_formation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};