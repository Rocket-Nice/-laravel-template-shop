<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cat_in_bag_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('total')->default(0);
            $table->json('visible_category_ids')->nullable();
            $table->unsignedTinyInteger('bag_count')->default(0);
            $table->unsignedTinyInteger('open_limit')->default(0);
            $table->unsignedTinyInteger('opened_count')->default(0);
            $table->string('status', 32)->default('active');
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cat_in_bag_sessions');
    }
};
