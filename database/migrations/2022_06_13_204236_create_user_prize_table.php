<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPrizeTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_prize', function (Blueprint $table) {
      $table->unsignedBigInteger('raffle_member_id');
      $table->unsignedBigInteger('prize_id')->unique();
      $table->timestamps();

      $table->primary(['prize_id']);
      $table->foreign('raffle_member_id')
          ->references('id')
          ->on('raffle_members')
          ->onDelete('cascade');
      $table->foreign('prize_id')
          ->references('id')
          ->on('prizes')
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
    Schema::dropIfExists('user_prize');
  }
}
