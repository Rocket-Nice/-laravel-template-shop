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
        Schema::create('dasha_mail_lists_users', function (Blueprint $table) {
          $table->unsignedBigInteger('dasha_mail_list_id');
          $table->unsignedBigInteger('user_id');
          $table->timestamps();

          $table->primary(['dasha_mail_list_id', 'user_id']);
          $table->foreign('dasha_mail_list_id')
              ->references('id')
              ->on('dasha_mail_lists')
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
        Schema::dropIfExists('dasha_mail_lists_users');
    }
};
