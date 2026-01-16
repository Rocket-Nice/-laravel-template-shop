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
        Schema::create('mailing_lists_users', function (Blueprint $table) {
          $table->unsignedBigInteger('mailing_list_id');
          $table->unsignedBigInteger('user_id');
          $table->timestamps();

          $table->primary(['mailing_list_id', 'user_id']);
          $table->foreign('mailing_list_id')
              ->references('id')
              ->on('mailing_lists')
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
        Schema::dropIfExists('mailing_lists_users');
    }
};
