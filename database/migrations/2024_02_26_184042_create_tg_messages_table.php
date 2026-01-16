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
        Schema::create('tg_messages', function (Blueprint $table) {
            $table->id();
          $table->unsignedBigInteger('tg_chat_id');
          $table->unsignedBigInteger('user_id');
          $table->unsignedBigInteger('tg_message_id')->nullable();
          $table->text('text')->nullable();
          $table->dateTime('time')->nullable();
          $table->json('data')->nullable();
          $table->boolean('outgoing_message')->default(true);
          $table->boolean('delivered')->default(false);
          $table->timestamps();

          $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
          $table->foreign('tg_chat_id')
              ->references('id')
              ->on('tg_chats')
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
        Schema::dropIfExists('tg_messages');
    }
};
