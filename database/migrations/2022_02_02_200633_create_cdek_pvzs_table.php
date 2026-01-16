<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCdekPvzsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdek_pvzs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type');
            $table->string('country_code')->nullable();
            $table->integer('region_code')->nullable();
            $table->string('region')->nullable();
            $table->integer('city_code')->nullable();
            $table->string('city')->nullable();
            $table->string('fias_guid')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('longitude')->nullable();
            $table->string('address')->nullable();
            $table->string('address_full')->nullable();
            $table->json('pvz_data')->nullable();
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
        Schema::dropIfExists('cdek_pvzs');
    }
}
