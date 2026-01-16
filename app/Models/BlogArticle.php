<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class BlogArticle extends Model
{
    use HasFactory, Loggable;
  use QueryCacheable;

  public $cacheFor = 3600 * 24;

  public function getRouteKeyName()
  {
    return 'slug';
  }
    public function category()
    {
      return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
    protected $casts = [
        'data_content' => 'array',
        'data_title' => 'array',
        'params' => 'array',
    ];

    protected $fillable = [
        'blog_category_id',
        'title',
        'content',
        'data_content',
        'data_title',
        'params',
        'status',
        'slug',
    ];
}
