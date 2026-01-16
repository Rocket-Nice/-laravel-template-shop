<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCoupon extends Model
{
    use HasFactory;

  public function pickup()
  {
    return $this->belongsTo('App\Models\Pickup');
  }
  public function order()
  {
    return $this->belongsTo('App\Models\Order');
  }
  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  public function giftCoupons() {
    return $this->hasMany('App\Models\GiftCoupon', 'order_id', 'order_id');
  }

  protected $casts = [
      'data' => 'array'
  ];

  protected $fillable = [
      'code',
      'user_id',
      'pickup_id',
      'order_id',
      'data',
  ];
}
