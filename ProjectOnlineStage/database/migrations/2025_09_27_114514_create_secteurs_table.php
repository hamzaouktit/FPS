<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('secteurs', function (Blueprint $table) {
            $table->string('nom_secteur')->primary();
            $table->string('code_efp');
            $table->foreign('code_efp')->references('code_efp')->on('etablissements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('secteurs');
    }
};