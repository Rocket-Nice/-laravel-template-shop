<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class CatInBagPrize extends Model
{
    use HasFactory;
    use QueryCacheable;

    public $cacheFor = 3600 * 24;

    protected $casts = [
        'data' => 'array',
        'is_enabled' => 'boolean',
        'is_golden' => 'boolean',
        'is_certificate' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'image',
        'total_qty',
        'used_qty',
        'category_id',
        'product_id',
        'is_enabled',
        'is_golden',
        'is_certificate',
        'data',
    ];

    public function category()
    {
        return $this->belongsTo(CatInBagCategory::class, 'category_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getAvailableQtyAttribute(): int
    {
        return max(0, (int)$this->total_qty - (int)$this->used_qty);
    }
}
