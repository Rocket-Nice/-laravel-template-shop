<?php

namespace App\Services;

use App\Models\Coupone;
use App\Models\Order;
use Illuminate\Support\Str;

class Promo113
{
  static public function gift(Order $order){
    $total = $order->data['total'] ?? 0;
    $amount = 0;
    if($total >= 10000){
      $amount = 1000;
    }elseif($total >= 5000){
      $amount = 500;
    }
    if($amount){
      $code = Str::random(4).'-'.Str::random(4);
      $code = mb_strtolower($code);
      $coupon = Coupone::create([
          'code' => $code,
          'type' => 10,
          'amount' => $amount,
          'count' => 1,
          'available_from' => '2025-11-01 00:00:00',
          'available_until' => '2025-11-30 23:59:59',
          'owner_id' => $order->user_id,
          'owner_order_id' => $order->id,
      ]);
    }
  }
}
