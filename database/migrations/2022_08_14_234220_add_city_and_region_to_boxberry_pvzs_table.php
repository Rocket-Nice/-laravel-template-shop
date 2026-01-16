<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityAndRegionToBoxberryPvzsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boxberry_pvzs', function (Blueprint $table) {
          $table->unsignedBigInteger('region_id')->nullable();
          $table->unsignedBigInteger('city_id')->nullable();

          $table->foreign('region_id')
              ->references('id')
              ->on('boxberry_regions')
              ->onDelete('set null');
          $table->foreign('city_id')
              ->references('id')
              ->on('boxberry_cities')
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
        Schema::table('boxberry_pvzs', function (Blueprint $table) {
            //
        });
    }
}
