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
        // Table Modules
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code_module');
            $table->string('nom_module');
            $table->enum('regional', ['O', 'N']); // O=Oui, N=Non
            $table->string('efp_pie')->nullable();
            $table->enum('module_pie', ['O', 'N'])->default('N'); // O=Oui, N=Non 
            $table->string('code_efp')->nullable();
             $table->foreign('code_efp')
                  ->references('code_efp')
                  ->on('etablissements')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
