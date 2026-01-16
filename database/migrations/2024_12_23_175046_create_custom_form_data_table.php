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
        Schema::create('custom_form_data', function (Blueprint $table) {
            $table->id();
            $table->text('value');
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('field_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->foreign('field_id')
                ->references('id')
                ->on('custom_form_fields')
                ->onDelete('cascade');
            $table->foreign('form_id')
                ->references('id')
                ->on('custom_forms')
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
        Schema::dropIfExists('custom_form_data');
    }
};
