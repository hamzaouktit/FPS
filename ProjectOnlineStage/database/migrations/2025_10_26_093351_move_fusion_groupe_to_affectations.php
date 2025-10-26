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
        // 1. Ajouter les colonnes fusion_groupe et code_fusion à la table affectations
        Schema::table('affectations', function (Blueprint $table) {
            $table->string('fusion_groupe')->nullable()->after('code_efp');
            $table->string('code_fusion')->nullable()->after('fusion_groupe');
        });

        // 2. Supprimer les colonnes de la table groupes
        Schema::table('groupes', function (Blueprint $table) {
            $table->dropColumn(['fusion_groupe', 'code_fusion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer les colonnes dans groupes
        Schema::table('groupes', function (Blueprint $table) {
            $table->string('fusion_groupe')->nullable();
            $table->string('code_fusion')->nullable();
        });

        // Supprimer les colonnes d'affectations
        Schema::table('affectations', function (Blueprint $table) {
            $table->dropColumn(['fusion_groupe', 'code_fusion']);
        });
    }
};