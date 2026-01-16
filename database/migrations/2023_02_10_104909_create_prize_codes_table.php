<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrizeCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prize_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prize_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code');
            $table->json('data')->nullable();
            $table->timestamps();

          $table->foreign('order_id')
              ->references ('id')
              ->on('orders')
              ->onDelete('cascade');
          $table->foreign('prize_id')
              ->references('id')
              ->on('prizes')
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
        Schema::dropIfExists('prize_codes');
    }
}
