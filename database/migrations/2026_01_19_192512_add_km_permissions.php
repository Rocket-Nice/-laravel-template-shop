<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::firstOrCreate([
            'name' => 'Доступ к КМ',
            'guard_name' => 'web',
        ]);

        Permission::firstOrCreate([
            'name' => 'Управление КМ',
            'guard_name' => 'web',
        ]);

        Permission::firstOrCreate([
            'name' => 'Управление подарками КМ',
            'guard_name' => 'web',
        ]);
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Permission::whereIn('name', [
            'Доступ к КМ',
            'Управление КМ',
            'Управление подарками КМ',
        ])->delete();
    }
};
