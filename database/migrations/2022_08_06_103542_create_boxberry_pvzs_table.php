<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxberryPvzsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxberry_pvzs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('work_schedule')->nullable();
            $table->text('trip_description')->nullable();
            $table->integer('delivery_period')->nullable();
            $table->string('city_code')->nullable();
            $table->string('city_name')->nullable();
            $table->string('tariff_zone')->nullable();
            $table->string('settlement')->nullable();
            $table->string('area')->nullable();
            $table->string('country')->nullable();
            $table->string('only_prepaid_orders')->nullable();
            $table->text('address_reduce')->nullable();
            $table->string('acquiring')->nullable();
            $table->string('digital_signature')->nullable();
            $table->string('type_of_office')->nullable();
            $table->string('nalKD')->nullable();
            $table->string('metro')->nullable();
            $table->float('volume_limit')->nullable();
            $table->integer('load_limit')->nullable();
            $table->string('GPS')->nullable();
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
        Schema::dropIfExists('boxberry_pvzs');
    }
}
