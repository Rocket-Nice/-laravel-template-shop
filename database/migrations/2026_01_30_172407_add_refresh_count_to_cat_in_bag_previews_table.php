<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cat_in_bag_previews', function (Blueprint $table) {
            $table->unsignedTinyInteger('refresh_count')->default(0)->after('category_ids');
        });
    }

    public function down(): void
    {
        Schema::table('cat_in_bag_previews', function (Blueprint $table) {
            $table->dropColumn('refresh_count');
        });
    }
};
