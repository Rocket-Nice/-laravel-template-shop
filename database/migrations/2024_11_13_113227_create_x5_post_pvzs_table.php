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
        Schema::create('x5_post_pvzs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->uuid('pvz_id')->unique();
            $table->string('mdmCode')->unique();
            $table->string('name')->unique();
            $table->string('partnerName')->nullable();
            $table->string('multiplaceDeliveryAllowed')->nullable();
            $table->string('type')->nullable();
            $table->string('country')->nullable();
            $table->string('fullAddress')->nullable();
            $table->string('shortAddress')->nullable();
            $table->string('address_lat')->nullable();
            $table->string('address_lng')->nullable();
            $table->text('additional')->nullable();
            $table->json('cellLimits')->nullable();
            $table->boolean('returnAllowed')->nullable();
            $table->string('timezoneOffset')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('cashAllowed')->nullable();
            $table->boolean('cardAllowed')->nullable();
            $table->boolean('loyaltyAllowed')->nullable();
            $table->string('extStatus')->nullable();
            $table->json('rate')->nullable();
            $table->dateTime('createDate')->nullable();
            $table->dateTime('openDate')->nullable();
            $table->string('timezone')->nullable();
            $table->boolean('outsideX5')->nullable();
            $table->timestamps();

          $table->foreign('region_id')
              ->references('id')
              ->on('x5_post_regions')
              ->onDelete('set null');
          $table->foreign('city_id')
              ->references('id')
              ->on('x5_post_cities')
              ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('x5_post_pvzs');
    }
};
