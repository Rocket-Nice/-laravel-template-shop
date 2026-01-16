<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ShortLink extends Model
{
    use HasFactory;
  use QueryCacheable;

  public $cacheFor = 10;

  public function getCode(){
    $code = Str::random(5);
    if(ShortLink::where('slug', $code)->count()){
      $this->getCode();
    }
    return $code;
  }

  public function getRouteKeyName()
  {
    return 'slug';
  }

  protected $casts = [
      'data' => 'array'
  ];

    protected $fillable = [
      'slug', 'link', 'data'
    ];
}
