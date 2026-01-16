<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('telegram_mailings', function (Blueprint $table) {
          $table->unsignedBigInteger('mailing_id')->nullable();

          $table->foreign('mailing_id')
              ->references('id')
              ->on('mailing_lists')
              ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegram_mailings', function (Blueprint $table) {
            //
        });
    }
};
