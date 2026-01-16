<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prize_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code')->unique();
            $table->integer('active')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

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
        Schema::dropIfExists('gift_codes');
    }
}
