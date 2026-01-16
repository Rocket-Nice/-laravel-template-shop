<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_invoice', function (Blueprint $table) {
          $table->unsignedBigInteger('order_id');
          $table->unsignedBigInteger('invoice_id');
          $table->timestamps();

          $table->primary(['order_id', 'invoice_id']);
          $table->foreign('order_id')
              ->references('id')
              ->on('orders')
              ->onDelete('cascade');
          $table->foreign('invoice_id')
              ->references('id')
              ->on('invoices')
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
        Schema::dropIfExists('order_invoice');
    }
}
