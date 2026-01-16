<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCdekRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdek_regions', function (Blueprint $table) {
            $table->id();
            $table->string('country_code')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->integer('region_code')->unique();
            $table->string('fias_region_guid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdek_regions');
    }
}
