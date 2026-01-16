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
        Schema::create('telegram_mailings', function (Blueprint $table) {
            $table->id();
            $table->text('message');
            $table->string('image')->nullable();
            $table->dateTime('send_at')->nullable();
            $table->json('filter')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_mailings');
    }
};
