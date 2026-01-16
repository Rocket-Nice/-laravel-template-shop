<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaffleMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raffle_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('prize_id')->nullable();
          $table->unsignedBigInteger('order_id')->nullable();
            $table->string('code')->unique();
          $table->integer('conditions')->nullable();
            $table->integer('count')->default(1);
            $table->timestamps();



          $table->foreign('order_id')
              ->references('id')
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
        Schema::dropIfExists('raffle_members');
    }
}
