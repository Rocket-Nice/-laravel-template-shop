<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class BlogCategory extends Model
{
    use HasFactory;
  use QueryCacheable;

  public $cacheFor = 3600 * 24;

  public function getRouteKeyName()
  {
    return 'slug';
  }
    public function articles()
    {
      return $this->hasMany(BlogArticle::class, 'blog_category_id');
    }

    protected $casts = [
        'data' => 'array'
    ];
    protected $fillable = [
        'name',
        'image',
        'status',
        'data',
        'slug',
    ];
}
