<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Category extends Model
{
    use HasFactory;
    use QueryCacheable;

  public $cacheFor = 3600 * 24;
    public function getRouteKeyName()
    {
      return 'slug';
    }

    public function products(){
      return $this->hasMany('App\Models\Product');
    }

//    public function hasCategories(){
//      return $this->belongsToMany(Category::class, 'category_category', 'main_category_id', 'child_id');
//    }
//
//    public function hasProducts(){
//      return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');
//    }

    public function parent()
    {
      return $this->belongsTo(Category::class, 'category_id');
    }

    public function categories()
    {
      return $this->hasMany(Category::class);
    }

    public function childrenCategories()
    {
      return $this->hasMany(Category::class)->with('categories');
    }

    public function scopeCatalog(Builder $builder){
      $builder->select('title', 'slug', 'options')->where('hidden', false)->orderBy('order');
    }

  static public function getAllParentIds(self $category, $ids = [])
  {
    if ($category->parent) {
      $ids[] = $category->parent->id;
      return self::getAllParentIds($category->parent, $ids);
    }

    return $ids;
  }

    protected $casts = [
        'options' => 'array'
    ];

    protected $fillable=[
        'title',
        'description',
        'category_id',
        'options',
        'template',
        'hidden',
        'slug',
        'order',
    ];
}
