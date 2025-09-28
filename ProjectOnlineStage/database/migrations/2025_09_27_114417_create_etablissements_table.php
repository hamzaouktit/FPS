<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('etablissements', function (Blueprint $table) {
            $table->string('code_efp', 10)->primary();
            $table->string('nom_efp', 100)->nullable(false);
            $table->foreignId('complexe_id')->constrained('complexes')->onDelete('cascade');
            $table->foreignId('user_id')
                  ->unique()
                  ->constrained('users')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('etablissements');
    }
};