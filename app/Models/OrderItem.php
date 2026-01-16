<?php

namespace App\Models;

use App\Models\Traits\FilterOrderItems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory, FilterOrderItems;

    public function order(){
      return $this->belongsTo(Order::class);
    }
    public function product(){
      return $this->belongsTo(Product::class);
    }
    public function items(){
      return $this->hasMany(OrderItem::class, 'parent_id');
    }
    static function setParams($order_id, $item, $parent_id = null){
      $params = [];
      $params['order_id'] = $order_id;
      $params['parent_id'] = $parent_id;
      foreach($item as $key => $item_param){
        if($key == 'builder'){
          continue;
        }
        if($key == 'id'){
          $params['product_id'] = $item_param;
          continue;
        }
        if(in_array($key, ['model', 'qty', 'price', 'name', 'product_id'])){
          $params[$key] = $item_param;
        }else{
          $params['params'][$key] = $item_param;
        }
      }
      if (!isset($params['price'])){
        $params['price'] = 0;
      }

      $orderItem = self::select('id')
          ->where('order_id', $params['order_id'])
          ->where('product_id', $params['product_id'])
          ->where('parent_id', $params['parent_id'])
          ->where('model', $params['model'])
          ->where('qty', $params['qty'])
          ->where('price', $params['price'])
          ->where('params->raffle', $item['raffle'] ?? null)
          ->first();
      if(!$orderItem) {
        $orderItem = self::create($params);
      }
      return $orderItem->id;
    }
    protected $casts = [
        'params' => 'array'
    ];
    protected $fillable = [
        'order_id',
        'product_id',
        'parent_id',
        'model',
        'name',
        'price',
        'qty',
        'params'
    ];
}
