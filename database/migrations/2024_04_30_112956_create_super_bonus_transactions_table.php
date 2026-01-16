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
        Schema::create('super_bonus_transactions', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('bonus_id');
          $table->unsignedBigInteger('user_id');
          $table->integer('amount');
          $table->string('comment', 250)->nullable();
          $table->timestamps();

          $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
          $table->foreign('bonus_id')
              ->references('id')
              ->on('bonuses')
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
        Schema::dropIfExists('super_bonus_transactions');
    }
};
