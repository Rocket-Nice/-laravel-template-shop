<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupone extends Model
{
    use HasFactory, Loggable;

  public function order()
  {
    return $this->belongsTo('App\Models\Order');
  }
  public function owner()
  {
    return $this->belongsTo('App\Models\User', 'owner_id', 'id');
  }

  public function partner()
  {
    return $this->hasOne('App\Models\Partner');
  }


  public function products()
  {
    return $this->belongsToMany(Product::class, 'coupone_product', 'coupone_id', 'product_id');
  }

  protected $casts = [
      'available_from' => 'datetime:Y-m-d H:i:s',
      'available_until' => 'datetime:Y-m-d H:i:s',
      'used_at' => 'datetime:Y-m-d H:i:s',
      'options' => 'array'
  ];

    protected $fillable = [
        'code',
        'type',
        'amount',
        'count',
        'used_at',
        'order_id',
        'options',
        'available_from',
        'available_until',
        'created_at',
        'owner_id',
        'owner_order_id',
    ];
}
