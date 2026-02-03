<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        ProductType::query()->dontCache()->firstOrCreate(
            ['name' => 'Подарок "Кот в мешке"'],
            ['data' => []]
        );
        ProductType::flushQueryCache();
    }
}
