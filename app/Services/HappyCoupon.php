<?php

namespace App\Services;

use App\Models\GiftCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prize;
use App\Models\Product;
use App\Models\Voucher;
use App\Models\PrizeCode;
use Illuminate\Support\Facades\DB;

class HappyCoupon
{
  public function setPrizeToOrder(Order $order){
    $user = $order->user;
    $order_data = $order->data;
    $data_cart = $order->data_cart;
    $update_cart = false;
    $all_ids = Prize::GENERAL;
    $expencive_gifts = Prize::EXPENSIVE;
    $empty = [194, 195]; // id пустого подарка
    $random = mt_rand(1, 100);
    if ($order_data['total'] >= 3599 && $order_data['total'] < 5999) {
      $count_coupones = 1;
      $win_coupones = [
          Prize::GENERAL
      ];
//      if($random <= 70){
//        $win_coupones = [
//            'all'
//        ];
//      }else{
//        $win_coupones = [
//            $empty
//        ];
//      }
//      return false;
    }elseif($order_data['total'] >= 5999){
      $count_coupones = 1;
      $win_coupones = [
          Prize::GENERAL2
      ];
//      if($random <= 70){
//        $win_coupones = [
//            'all'
//        ];
//      }else{
//        $win_coupones = [
//            $empty
//        ];
//      }
    }
    // мальдивы
//    if(in_array($order->user_id, [195009,335503,348319]) && $user->giftCoupons()->where('prize_id', 197)->count()==0){
////      $win_coupones[0] = [197];
//    }elseif(in_array($order->user_id, [362654,236723,362652]) && $user->giftCoupons()->where('prize_id', 198)->count()==0){
////      $win_coupones[0] = [198];
//    }
    $order_coupons = $order->giftCoupons()->count();
    $member_codes = [];
    if ($order_coupons >= $count_coupones) {
      foreach ($order->giftCoupons as $coupone) {
        $member_codes[] = $coupone->code;
      }
      //$member_code = $order->raffle_member->code;
    } else {
      // исключаем дорогие подарки
      if ($user->giftCoupons()->where('created_at', '>', '2024-12-18')->whereIn('prize_id', $expencive_gifts)->count()>0){
        $exclude = $expencive_gifts;
      }else{
        $exclude = [];
      }
      // исключаем количество уже добавленых подарков к заказу
      if ($order_coupons > 0){
        $win_coupones = array_splice($win_coupones, $order_coupons);
      }
      //
//      if($order->giftCoupons()->where('prize_id', $empty)->count() >= 2){
//        $exclude = array_merge($exclude, [$empty]);
//      }
      shuffle($win_coupones); // массив в случайном порядке
      foreach($win_coupones as $win_item){
        $member_code = getCode(12);
        // если пустой купон в исключениях, то выдем все подряд
//        if(in_array($empty, $exclude)){
//          $win_item = 'all';
//        }
        // если all то переводим точный список id доступных подарков
        if($win_item == 'all'){
          $win_item = $all_ids;
        }
        $gifts = Prize::query()
            ->selectRaw("
                id,
                CASE 
                    WHEN id IN (" . implode(',', Prize::RED) . ") AND count > 0 THEN count + 150
                    ELSE count 
                END as count,
                total
            ")
            ->whereIn('id', $win_item)
            ->whereNotIn('id', $exclude)
            ->where('count', '>', 0)
            ->where('active', true)
            ->get()->toArray();
        $randomGift = $this->getRandomGiftId($gifts);
        $win_item = array_merge($randomGift, $expencive_gifts);
        $prize_id = $this->getGift($win_item, $exclude);

        if (!$prize_id) {
//            if($order_data['total'] >= 5000){
//              Log::debug($order->id);
//              Log::debug('$win_item'.print_r($win_item, true));
//              Log::debug('$exclude'.print_r($exclude, true));
//              Log::debug($order->id.' $prize_id - '.$prize_id);
//            }
//          $prize_id = $this->getGift($win_item, $exclude);
//          if (!$prize_id) {
//            if($order_data['total'] >= 5000){
//              Log::debug($order->id);
//              Log::debug('$win_item'.print_r($win_item, true));
//              Log::debug('$exclude'.print_r($exclude, true));
//              Log::debug($order->id.' $prize_id - '.$prize_id);
//            }
//            continue;
//          } elseif (in_array($prize_id, $expencive_gifts)) {
//            $exclude = array_merge($exclude, $expencive_gifts);
//          }
          // $prize_id = null;
          // не создаем пустые подарки'
          continue;
        } elseif (in_array($prize_id, $expencive_gifts)) {
          $exclude = array_merge($exclude, $expencive_gifts);
        }elseif(!in_array($prize_id, $empty)){
          $exclude[] = $prize_id;
        }elseif(in_array($prize_id, $empty)){
          $added_to_exclude = false;
          foreach($empty as $empty_id){
            if(in_array($empty_id, $exclude)){
              $added_to_exclude = true;
              break;
            }
          }
          if(!$added_to_exclude){
            $exclude[] = $prize_id;
          }
        }
//        if($prize_id==$empty){
//          $pp_menu++;
//        }
//        if($pp_menu > 0 && Prize::where('active', 1)->where('count', '>', 0)->count() >= 3){
//          $exclude = array_merge($exclude, [22]);
//        }else if($pp_menu > 1 && Prize::where('active', 1)->where('count', '>', 0)->count() >= 2){
//          $exclude = array_merge($exclude, [22]);
//        }
        // если не пустой подарок и включено разнообразие больше 3х, то добавляем в исключения текущий подарок
//        if($prize_id != $empty && Prize::where('active', 1)->where('count', '>', 0)->count() >= 3){
//          // $exclude = array_merge($exclude, [$prize_id]);
//        }
//        // с вероятностью 90%, если уже есть пустой подарок
//        if($pp_menu > 1 && Prize::where('active', 1)->where('count', '>', 0)->count() >= 3){
//          $exclude = array_merge($exclude, [$empty]);
//        }
//        if(auth()->id()==1) {
//          continue;
//        }
//        if(auth()->id()==1){
//          return $win_coupones;
//        }
        $data = [];
        if(getSettings('promo20')){
          $data['position']['count'] = 1;
          $data['promo20'] = true;
        }
        $voucher_available_from = '2024-12-31 00:00:00';
        $voucher_available_until = '2025-06-30 23:59:59';
        if(in_array($prize_id, [173,131])){
          if ($prize_id==131){
            $voucher_amount = 1000;
          }elseif($prize_id==173){
            $voucher_amount = 5000;
          }
          Voucher::create([
              'code' => $member_code,
              'amount' => $voucher_amount,
              'type' => 1,
              'count' => 1,
              'available_until' => $voucher_available_until,
              'available_from' => $voucher_available_from,
              'options' => [
                  'raffle_member' => $member_code
              ]
          ]);
          $data['voucher'] = $member_code;
        }

        $add_member = GiftCoupon::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'prize_id' => $prize_id,
            'code' => $member_code,
            'data' => $data,
            'is_happy_coupon' => true
        ]);
        $member_codes[] = $add_member->code;
        if($prize_id){
          $prize = Prize::find($prize_id);
          if ($prize->product_id){
            $already_added = false;
            foreach($data_cart as $item){
              if (isset($item['raffle'])&&$item['raffle']==$member_code){
                $already_added = true;
              }
            }
            if(!$already_added){
              $product = Product::select('id', 'name', 'sku', 'price', 'style_page->cardImage->image->200 as image')->where('id', $prize->product_id)->first();
              $data_cart[] = [
                  "id" => $product->id,
                  "qty" => "1",
                  "name" => $product->name,
                  "model" => $product->sku,
                  "raffle" => $member_code,
                  "image" => $product->image,
                  "old_price" => $product->price,
                  "price" => 0
              ];
              $update_cart = true;
            }
          }
        }
      }

    }
    // удалить
//    if(auth()->id()==1) {
//      return $member_codes;
//    }
//    if(auth()->id()==1) {
//      foreach($order->giftCoupons()->pluck('prize_id', 'code')->toArray() as $prize_id){
//        $prize = Prize::find($prize_id);
//        echo ($prize->name ?? 'пустой').'<br/>';
//      }
//       $order->giftCoupons()->delete();
//      $exclude = $exclude ?? [];
//      dd($member_codes, $exclude, $order->giftCoupons()->pluck('prize_id', 'code')->toArray());
//    }

    // end удалить
    if ($update_cart){
      $order->update([
          'data_cart' => $data_cart,
      ]);
      foreach($data_cart as $cart_item){
        OrderItem::setParams($order->id, $cart_item);
      }
    }
    return $member_codes;
  }

  function getRandomGiftId(&$gifts) {
    usort($gifts, function ($a, $b) {
      return $b['count'] <=> $a['count'];
    });
//    dd($gifts);
    $totalQuantity = array_sum(array_column($gifts, 'count'));

    while ($totalQuantity > 0) {
      // Генерируем случайное число в пределах общего количества
      $random = mt_rand(1, $totalQuantity);
      // Определяем выбранный подарок на основе случайного числа
      foreach ($gifts as &$gift) {
        if ($random <= $gift['count']) {
          return [$gift['id']];
        }
        $random -= $gift['count'];
      }

      // Обновляем общее количество подарков после исключения недоступных
      $totalQuantity = array_sum(array_column($gifts, 'count'));
    }
    return null;
  }

  public function getGift($include = null, $exclude = [])
  {
    $prize_id = null;
    $prize = null;
    $firstFind = [];
    if(is_numeric($include)){
      $prize_id = $include;
    }elseif(is_array($include)){
      $firstFind = $include;
    }
    if ($prize_id&&!in_array($prize_id, $exclude)) { //
      $query = DB::update('UPDATE `prizes` SET `count`=`count`-1 WHERE `count` > 0 AND `id` = ' . $prize_id . ';');
      if ($query > 0) {
        $prize = $prize_id;
      }
    }
    if (!$prize) {
      $prize = $this->queryGift($firstFind, $exclude);
    }
    return $prize;
  }

  private function queryGift($firstFind = [], $exclude = [])
  {
    $gift = Prize::query()
        ->select('id')
        ->where('active', 1)
        ->where('total', '>', 0)
        ->where('count', '>', 0);
    if (!empty($firstFind)){
      $gift->whereIn('id', $firstFind);
    }
    if (!empty($exclude)){
      $gift->whereNotIn('id', $exclude);
    }
    $gift = $gift->inRandomOrder()->first();
    if ($gift) {
      $query = DB::update('UPDATE `prizes` SET `count`=`count`-1 WHERE `count` > 0 AND `id` = ' . $gift->id . ';');
      if ($query > 0) {
        return $gift->id;
      } else {
        $this->queryGift($firstFind, $exclude);
      }
    }else{
      return NULL;
    }
  }
}
