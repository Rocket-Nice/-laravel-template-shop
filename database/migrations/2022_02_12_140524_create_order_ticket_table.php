<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTicketTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_ticket', function (Blueprint $table) {
          $table->unsignedBigInteger('order_id')->unique();
          $table->unsignedBigInteger('ticket_id');
          $table->timestamps();

          $table->primary(['order_id', 'ticket_id']);
          $table->foreign('order_id')
              ->references('id')
              ->on('orders')
              ->onDelete('cascade');
          $table->foreign('ticket_id')
              ->references('id')
              ->on('tickets')
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
        Schema::dropIfExists('order_ticket');
    }
}
