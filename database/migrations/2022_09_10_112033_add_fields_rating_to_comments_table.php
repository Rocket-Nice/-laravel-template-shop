<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsRatingToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
          $table->integer('rQuality')->min(1)->max(5)->nullable();
          $table->integer('rAroma')->min(1)->max(5)->nullable();
          $table->integer('rStructure')->min(1)->max(5)->nullable();
          $table->integer('rEffect')->min(1)->max(5)->nullable();
          $table->integer('rShipping')->min(1)->max(5)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            //
        });
    }
}
