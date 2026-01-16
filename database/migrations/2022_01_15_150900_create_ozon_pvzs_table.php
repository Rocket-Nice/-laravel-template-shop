<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOzonPvzsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ozon_pvzs', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('pvz_id')->unique();
          $table->unsignedBigInteger('objectTypeId')->nullable();
          $table->string('objectTypeName')->nullable();
          $table->string('name')->nullable();
          $table->text('description')->nullable();
          $table->string('address')->nullable();
          $table->string('region')->nullable();
          $table->string('settlement')->nullable();
          $table->string('streets')->nullable();
          $table->string('placement')->nullable();
          $table->boolean('enabled')->nullable();
          $table->integer('cityId')->nullable();
          $table->string('fiasGuid')->nullable();
          $table->string('fiasGuidControl')->nullable();
          $table->integer('addressElementId')->nullable();
          $table->boolean('fittingShoesAvailable')->nullable();
          $table->boolean('fittingClothesAvailable')->nullable();
          $table->boolean('cardPaymentAvailable')->nullable();
          $table->text('howToGet')->nullable();
          $table->integer('contractorId')->nullable();
          $table->string('stateName')->nullable();
          $table->integer('maxWeight')->nullable();
          $table->integer('maxPrice')->nullable();
          $table->integer('restrictionWidth')->nullable();
          $table->integer('restrictionLength')->nullable();
          $table->integer('restrictionHeight')->nullable();
          $table->string('lat')->nullable();
          $table->string('long')->nullable();
          $table->boolean('returnAvailable')->nullable();
          $table->boolean('partialGiveOutAvailable')->nullable();
          $table->boolean('dangerousAvailable')->nullable();
          $table->boolean('isCashForbidden')->nullable();
          $table->string('code')->nullable();
          $table->boolean('wifiAvailable')->nullable();
          $table->boolean('legalEntityNotAvailable')->nullable();
          $table->boolean('isRestrictionAccess')->nullable();
          $table->string('utcOffsetStr')->nullable();
          $table->boolean('isPartialPrepaymentForbidden')->nullable();
          $table->boolean('isGeozoneAvailable')->nullable();
          $table->string('postalCode')->nullable();
          $table->unsignedBigInteger('region_id')->nullable();
          $table->unsignedBigInteger('city_id')->nullable();
          $table->timestamps();

          $table->foreign('region_id')
              ->references('id')
              ->on('regions')
              ->onDelete('set null');
          $table->foreign('city_id')
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
        Schema::dropIfExists('ozon_pvzs');
    }
}
