<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('secteurs', function (Blueprint $table) {
            $table->string('nom_secteur', 50)->primary();
        });
    }

    public function down()
    {
        Schema::dropIfExists('secteurs');
    }
};