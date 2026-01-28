<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_in_bag_prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->unsignedInteger('total_qty')->default(0);
            $table->unsignedInteger('used_qty')->default(0);
            $table->foreignId('category_id')->constrained('cat_in_bag_categories')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_golden')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_in_bag_prizes');
    }
};
