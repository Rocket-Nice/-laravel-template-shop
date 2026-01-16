<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ProductType extends Model
{
    use HasFactory, QueryCacheable;

  public $cacheFor = 3600 * 24;

  public function products(){
    return $this->hasMany('App\Models\Product', 'type_id');
  }

    protected $casts = [
        'data' => 'array'
    ];
    protected $fillable = [
        'name', 'data'
    ];
}
