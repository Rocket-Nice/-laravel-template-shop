<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
  use HasFactory, Loggable;

  const EXPENSIVE = [198];//128
  const GENERAL = [176,152,153,154,155,176,188,185,212,186,214,215, ];
  const GENERAL2 = [165,199,200,201,202,203,204,205,206,207,208,209,210,211,213,184,196,187,216];
  const RED = [208, 214, 215, 209];//209 //154


  public function giftCoupons() {
    return $this->hasMany('App\Models\GiftCoupon');
  }

  public function product() {
    return $this->belongsTo('App\Models\Product');
  }

  protected $casts = [
      'options' => 'array'
  ];

  protected $fillable = [
      'name',
      'image',
      'count',
      'code',
      'total',
      'active',
      'product_id',
      'options',
  ];
}
