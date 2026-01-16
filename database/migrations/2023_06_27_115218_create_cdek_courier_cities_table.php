<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCdekCourierCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdek_courier_cities', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->unique();
            $table->string('city');
            $table->string('fias_guid')->nullable();
            $table->string('country_code')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->integer('region_code')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->string('fias_region_guid')->nullable();
            $table->string('sub_region')->nullable();
            $table->json('postal_codes')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('time_zone')->nullable();
            $table->string('payment_limit')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();

            $table->foreign('region_id')
                ->references('id')
                ->on('cdek_regions')
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
        Schema::dropIfExists('cdek_courier_cities');
    }
}
