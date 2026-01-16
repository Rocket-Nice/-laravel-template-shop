<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cdek_cities', function (Blueprint $table) {
          $table->unsignedBigInteger('lm_country_id')->nullable();
          $table->unsignedBigInteger('lm_region_id')->nullable();
          $table->unsignedBigInteger('lm_city_id')->nullable();

          $table->foreign('lm_country_id')
              ->references('id')
              ->on('countries')
              ->onDelete('set null');
          $table->foreign('lm_region_id')
              ->references('id')
              ->on('regions')
              ->onDelete('set null');
          $table->foreign('lm_city_id')
              ->references('id')
              ->on('cities')
              ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cdek_cities', function (Blueprint $table) {
            //
        });
    }
};
