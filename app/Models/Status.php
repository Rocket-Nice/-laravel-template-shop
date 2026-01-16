<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Status extends Model
{
    use HasFactory, QueryCacheable;

  public $cacheFor = 3600*24;

    protected $casts = [
        'data' => 'array'
    ];
    protected $fillable = [
        'key',
        'name',
        'color',
        'data',
        'finish',
        'success',
        'fail',
        'order'
    ];
}
