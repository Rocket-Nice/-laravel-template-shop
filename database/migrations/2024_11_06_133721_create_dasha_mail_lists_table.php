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
        Schema::create('dasha_mail_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('list_id')->unique();
            $table->string('name');
            $table->integer('count_subscribers')->default(0);
            $table->integer('count_active_subscribers')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dasha_mail_lists');
    }
};
