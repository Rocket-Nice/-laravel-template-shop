<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class CatInBagCategory extends Model
{
    use HasFactory;
    use QueryCacheable;

    public $cacheFor = 3600 * 24;

    protected $casts = [
        'data' => 'array',
        'is_enabled' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'image',
        'data',
        'is_enabled',
    ];

    public function prizes()
    {
        return $this->hasMany(CatInBagPrize::class, 'category_id');
    }
}
