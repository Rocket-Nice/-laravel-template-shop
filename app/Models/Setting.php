<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Setting extends Model
{
    use HasFactory, QueryCacheable, Loggable;

//   public $cacheFor = 3600 * 24;

//   public $cacheDriver = 'memcached';
    // public $cacheDriver = 'file';

    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = [
        'key',
        'value',
        'data'
    ];
}
