<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $exists = DB::table('settings')->where('key', 'catInBag')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'catInBag',
                'value' => '0',
                'data' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->where('key', 'catInBag')->delete();
    }
};
