<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Page extends Model
{
  use HasFactory, QueryCacheable, Loggable;

  public $cacheFor = 60*60*24;

  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function users()
  {
    return $this->belongsToMany('App\Models\Page', 'page_user', 'page_id', 'user_id');
  }
  protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
  ];

  protected $fillable = [
      'title',
      'content',
      'slug'
  ];
}
