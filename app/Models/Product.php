<?php

namespace App\Models;

use App\Models\Traits\Commentable;
use App\Models\Traits\FilterProduct;
use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\SearchableTrait;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Product extends Model
{
  use HasFactory, QueryCacheable, Loggable, FilterProduct, SearchableTrait, Commentable;

  public $cacheFor = 3600 * 24;

  protected $searchable = [
      'columns' => [
          'products.keywords' => 10,
          'products.name' => 10, //
      ],
      'min_word_length' => 2
  ];


  public function productGroups()
  {
    return $this->belongsToMany(ProductGroup::class, 'product_group_product')->withPivot(['order']);
  }
  public function parent()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }
  public function optionProducts()
  {
    return $this->hasMany(Product::class, 'product_id');
  }
  # TODO: Переименовать article в sku
  # TODO: Удалить поле description

  public function product_sku(){
    return $this->belongsTo(ProductSku::class);
  }
  public function category()
  {
    return $this->belongsTo('App\Models\Category');
  }
  public function refill()
  {
    return $this->hasOne('App\Models\Product', 'main_product_id')->where('hidden', false);
  }
  public function main_product()
  {
    return $this->belongsTo('App\Models\Product', 'main_product_id');
  }
  public function categories(){
    return $this->belongsToMany('App\Models\Category', 'category_product','product_id', 'category_id');
  }
  public function hasCategory($categoryId)
  {
    return $this->categories()->where('id', $categoryId)->exists();
  }
  public function type()
  {
    return $this->belongsTo('App\Models\Type', 'type_id');
  }
  public function product_notifications() {
    return $this->hasMany('App\Models\ProductNotification');
  }
  public function getRouteKeyName()
  {
    return 'slug';
  }
  public function scopeCatalog(Builder $builder, $category_id = null, $order = true){
    $select = ['products.id',
        'name',
        'category_id',
        'type_id',
        'sku',
        'volume',
        'style_cards',
        'style_page->subtitle as subtitle',
        'style_page->cardImage as cardImage',
        'style_page->cardsDescription as cardsDescription',
        'style_page->cardsDescriptionIcons as cardsDescriptionIcons',
        'product_options',
        'slug',
        'status',
        'quantity',
        'data_status',
        'data_quantity',
        'price',
        'old_price',
        'hidden',
        'preorder',
        'data_quantity->promotion as promoQuantity',
        'data_status->promotion as promoStatus',
        'options->puzzles as puzzles',
        'options->only_pickup as only_pickup',
        'options->gold_coupon as gold_coupon',
        'options->new_structure as new_structure',
        'options->soon as soon',
        'options->is_new as is_new',
        'options->sale as sale',
        'options->tag20 as tag20',
        'options->tag30 as tag30',
        'options->tag50 as tag50',
        'options->puzzles_count as puzzles_count',
        'style_page->puzzle_color as puzzle_color'
    ];
    $builder->select($select)
    ->with('refill');

    if(auth()->check()){
      $builder->with(['product_notifications' => function ($query) {
        $query->where('was_noticed', false)
            ->where('user_id', auth()->id());
      }]);
    }

    $builder->whereNotNull('style_cards');
    $builder->where('hidden', false);
    //$builder->where('price', '>', 0);
    if(request()->search && mb_strlen(request()->search) >= 2){
      $select[] = 'relevance';
      $builder->select($select);
      if((!auth()->check()||!auth()->user()->hasPermissionTo('Доступ к админпанели'))&&!SearchQuery::query()->where('query', request()->search)->where('ip', request()->ip())->where('created_at', '>', now()->subMinutes(10)->format('Y-m-d H:i:s'))->exists()){
        SearchQuery::create([
            'query' => request()->search,
            'ip' => request()->ip()
        ]);
      }

      $keywords = explode(' ', request()->search);
      foreach($keywords as $key => $keyword){
        $keywords[$key] = clean_search_string($keyword);
      }
      $keywords = implode(' ', $keywords);
      $builder->search($keywords);
      if($builder->count()>0){
        $max_relevance = $builder->max('relevance');
        $builder->where('relevance', '>=', $max_relevance);
      }
    }
    if(request()->get('category') || $category_id){

      $category_id = request()->get('category') ?? $category_id;
      $builder->where(function($query) use ($category_id) {
        $query->where('category_id', $category_id)
            ->orWhereHas('categories', function($q) use ($category_id) {
              $q->where('categories.id', $category_id);
            });
      });
    }
    if(request()->discount){
      $builder->where('old_price', '>', 'price');
    }
    if(request()->in_stock){
      $builder->where('quantity', '>', 0);
      $builder->where('status', true);
    }
    if(request()->preorder){
      $builder->where('preorder', true);
    }
    if(request()->minPrice){
      $builder->where('price', '>=', request()->minPrice);
    }
    if(request()->maxPrice){
      $builder->where('price', '<=', request()->maxPrice);
    }
    $builder->whereIn('type_id', [1,9]);
//    $builder->orderByDesc('quantity');
//    if(!request()->search){
//
//    }
    if(request()->orderBy){
      $orderBy =  explode('|', request()->orderBy);
      if(isset($orderBy[1])&&in_array($orderBy[1], ['asc', 'desc'])){
        $builder->orderByDesc('status')->orderBy($orderBy[0], $orderBy[1]);
      }else{
        $builder->orderByDesc('status')->orderBy('order', 'desc');
      }
    }elseif($order){
      $builder
          ->orderByRaw('CASE 
                  WHEN status = true AND quantity > 0 THEN 0 
                  ELSE 1 
              END')
          ->orderBy('products.order', 'desc');
    }
  }
  public function getPrice(){
    return $this->price;
  }
  public function orderItems(){
    return $this->hasMany(OrderItem::class);
  }
  public function getRating($full = false)
  {
    $rating = $this->comments()->where(function ($query) {
      $query->where('hidden', false);
      $query->orWhere('hidden', null);
    });
    $rating = (round($rating->avg('rQuality'), 1) + round($rating->avg('rAroma'), 1) + round($rating->avg('rStructure'), 1) + round($rating->avg('rEffect'), 1) + round($rating->avg('rShipping'), 1)) / 5;
    $result = round($rating, 1);
    return $result;
  }

  public function getStock($shipping_code = null, $qty = null)
  {

    if(!$this->price){
      return false;
    }
    if ($shipping_code) {
      $result = true;
      if (in_array($shipping_code, ['cdek', 'cdek_courier', 'boxberry', 'x5post', 'pochta', 'pickup', 'nt', 'yandex'])) {
        if ($this->quantity < $qty || !$this->status) {
          $result = false;
        }
      } else {
        $pickup = Pickup::where('code', $shipping_code)->first();
        if ($pickup) {
          $quantity = $this->data_quantity[$pickup->params['quantity']] ?? 0;
          $status = $this->data_status[$pickup->params['status']] ?? 0;
        }
        if (!$pickup || ($pickup && ($quantity < $qty || $status != 1))) {
          $result = false;
        }
      }
    } else {
      $result = false;
      if ($this->status && $this->quantity > 0) {
        $result = true;
      }
      $pickups = Pickup::where('params->status', '!=', null)->where('params->quantity', '!=', null)->get();
      foreach ($pickups as $pickup) {
        if ((isset($this->data_status[$pickup->params['status']]) && $this->data_status[$pickup->params['status']]) && (isset($this->data_quantity[$pickup->params['quantity']]) && $this->data_quantity[$pickup->params['quantity']] > 0)) {
          $result = true;
        }
      }
    }

    return $result;
  }

  public function cleanKeywords(): void
  {
    if(!$this->keywords || !$this->name || !$this->id){
      return;
    }
    $keywords = $this->keywords;
    $keywords = explode(' ', $keywords);
    foreach($keywords as $key => $keyword){
      $keywords[$key] = mb_strtolower(clean_search_string($keyword));
      if(!$keyword){
        unset($keywords[$key]);
      }
    }
    $uniqueKeywords = array_unique($keywords);
    $name = $this->name;
    $name = str_replace('-',' ', $name);
    $name = explode(' ', $name);
    foreach($name as $key => $n){
      $name[$key] = mb_strtolower(clean_search_string($n));
      if(!$n){
        unset($name[$key]);
      }
    }
    $uniqueName = array_unique($name);
    $diff = array_diff($uniqueKeywords, $uniqueName);
    $this->update([
        'keywords' => implode(', ', $diff)
    ]);
  }


  const MARKETPLACE_STATUS = [
      1 => 'Да',
      2 => 'Нет',
      3 => 'Скрыт',
  ];
  protected $casts = [
      'options' => 'array',
      'data_page' => 'array',
      'data_quantity' => 'array',
      'data_status' => 'array',
      'product_options' => 'array',
      'style_cards' => 'array',
      'cardImage' => 'array',
      'style_page' => 'array',
  ];
  protected $fillable = [
      'name',
      'sku',
      'product_sku_id',
      'quantity',
      'price',
      'old_price',
      'volume',
      'weight',
      'tnved',
      'status',
      'options',
      'preorder',
      'product_options',
      'data_page',
      'data_quantity',
      'style_cards',
      'style_page',
      'data_status',
      'template',
      'category_id',
      'product_id',
      'main_product_id',
      'type_id',
      'hidden',
      'keywords',
      'order',
      'slug',
      'is_producing',
      'in_stock_wb',
      'in_stock_ozon',
      'comment',
  ];
}
