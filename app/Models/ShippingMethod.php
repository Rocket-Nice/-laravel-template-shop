<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ShippingMethod extends Model
{
    use HasFactory, QueryCacheable;

    public $cacheFor = 3600*24;

    protected $fillable = [
        'code',
        'name',
        'add_price',
        'active'
    ];
}
