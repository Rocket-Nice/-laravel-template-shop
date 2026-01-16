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
        Schema::create('cdek_new_territories', function (Blueprint $table) {
            $table->id();
            $table->text('address')->nullable();
            $table->string('code')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('pvz_id')->nullable();
            $table->timestamps();

          $table->foreign('pvz_id')
              ->references('id')
              ->on('cdek_pvzs')
              ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdek_new_territories');
    }
};
