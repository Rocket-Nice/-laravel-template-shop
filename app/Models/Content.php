<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Content extends Model
{
    use HasFactory, QueryCacheable, Loggable;

    public $cacheFor = 3600*24;

    protected $casts = [
        'text_data' => 'array',
        'image_data' => 'array',
        'carousel_data' => 'array',
        'data' => 'array',
    ];

    protected $fillable = [
        'title',
        'route',
        'template_path',
        'active',
        'text_data',
        'image_data',
        'carousel_data',
        'keywords',
        'data',
    ];
}
