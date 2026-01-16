<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxberryCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxberry_cities', function (Blueprint $table) {
          $table->id();
          $table->string('Name');
          $table->string('Code')->unique();
          $table->integer('ReceptionLaP')->nullable();
          $table->integer('DeliveryLaP')->nullable();
          $table->integer('Reception')->nullable();
          $table->integer('ForeignReceptionReturns')->nullable();
          $table->integer('Terminal')->nullable();
          $table->integer('CourierReception')->nullable();
          $table->string('Kladr')->nullable();
          $table->string('Region')->nullable();
          $table->string('UniqName')->nullable();
          $table->string('District')->nullable();
          $table->json('data')->nullable();
          $table->unsignedBigInteger('region_id')->nullable();
          $table->timestamps();

          $table->foreign('region_id')
              ->references('id')
              ->on('boxberry_regions')
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
        Schema::dropIfExists('boxberry_cities');
    }
}
