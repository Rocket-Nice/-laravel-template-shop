<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_in_bag_prizes', function (Blueprint $table) {
            $table->boolean('is_certificate')->default(false)->after('is_golden');
        });
    }

    public function down(): void
    {
        Schema::table('cat_in_bag_prizes', function (Blueprint $table) {
            $table->dropColumn('is_certificate');
        });
    }
};
