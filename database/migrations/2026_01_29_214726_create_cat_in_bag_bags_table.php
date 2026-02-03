<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_in_bag_bags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('cat_in_bag_sessions')->cascadeOnDelete();
            $table->unsignedTinyInteger('position')->default(1);
            $table->string('type', 16)->default('normal');
            $table->unsignedTinyInteger('open_index')->nullable();
            $table->dateTime('opened_at')->nullable();
            $table->foreignId('prize_id')->nullable()->constrained('cat_in_bag_prizes')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('prize_type', 16)->nullable();
            $table->unsignedInteger('nominal')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'position']);
            $table->unique(['session_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_in_bag_bags');
    }
};
