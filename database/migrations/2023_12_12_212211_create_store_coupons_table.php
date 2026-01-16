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
        Schema::create('store_coupons', function (Blueprint $table) {
          $table->id();
          $table->string('code')->unique();
          $table->unsignedBigInteger('user_id')->nullable();
          $table->unsignedBigInteger('pickup_id')->nullable();
          $table->unsignedBigInteger('order_id')->nullable();
          $table->json('data')->nullable();
          $table->timestamps();



          $table->foreign('order_id')
              ->references('id')
              ->on('orders')
              ->onDelete('cascade');
          $table->foreign('pickup_id')
              ->references('id')
              ->on('pickups')
              ->onDelete('cascade');
          $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_coupons');
    }
};
