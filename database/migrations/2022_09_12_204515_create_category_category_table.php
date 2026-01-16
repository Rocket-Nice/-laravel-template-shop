<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_category', function (Blueprint $table) {
          $table->unsignedBigInteger('main_category_id');
          $table->unsignedBigInteger('child_id');
          $table->timestamps();

          $table->primary(['main_category_id', 'child_id']);
          $table->foreign('main_category_id')
              ->references('id')
              ->on('categories')
              ->onDelete('cascade');
          $table->foreign('child_id')
              ->references('id')
              ->on('categories')
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
        Schema::dropIfExists('category_category');
    }
}
