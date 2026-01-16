<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCityIdToCdekPvzsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cdek_pvzs', function (Blueprint $table) {
          $table->unsignedBigInteger('city_id')->after('city')->nullable();

          $table->foreign('city_id')
              ->references('id')
              ->on('cdek_cities')
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
        Schema::table('cdek_pvzs', function (Blueprint $table) {
            //
        });
    }
}
