<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductGroup extends Model
{
  public static function findOrCreate($name)
  {
    $productGroup = self::query()->where('name', Str::slug($name))->first();
    if(!$productGroup){
      $productGroup = self::create([
          'name' => $name
      ]);
    }
    return $productGroup;
  }
  public function products()
  {
    return $this->belongsToMany(Product::class, 'product_group_product')->withPivot(['order']);
  }
  public function pages()
  {
    return $this->belongsToMany(Content::class, 'content_product_group');
  }

  protected $fillable = [
      'name',
      'description'
  ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $model->name = Str::slug($model->name);
    });
    static::updating(function ($model) {
      $model->name = Str::slug($model->name);
    });
  }
}
