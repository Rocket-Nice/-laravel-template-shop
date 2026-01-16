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
        Schema::table('puzzle_images', function (Blueprint $table) {
          $table->unsignedBigInteger('member_id')->nullable();
          $table->boolean('has_prize')->default(false);
          $table->boolean('is_correct')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('puzzle_images', function (Blueprint $table) {
            //
        });
    }
};
