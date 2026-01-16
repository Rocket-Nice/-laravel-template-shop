<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\GiftCoupon;
use App\Models\Order;
use App\Models\Pickup;
use App\Models\Voucher;

class OrderController extends Controller
{
    public function index(){
      $user = auth()->user();
      $orders = $user->orders()->select('id', 'amount', 'confirm', 'created_at', 'status', 'slug', 'created_at')->where(function($query){
        $query->where('confirm', 1);
        $query->orWhere('created_at', '>', now()->subHour()->format('Y-m-d H:i:s'));
      })->orderBy('created_at', 'desc')->get();


      // подарочные сертификаты
      $certs = [];
      if($user->raffle_members()->where(function($query){
        $query->whereHas('order', function(\Illuminate\Database\Eloquent\Builder $builder){
          $builder->where('data->this_status->status', '!=', 'refund');
        })->orWhere('order_id', null);
      })->where('prize_id', 53)->count())
      {
        $user_gifts = $user->raffle_members()->where(function($query){
          $query->whereHas('order', function(\Illuminate\Database\Eloquent\Builder $builder){
            $builder->where('data->this_status->status', '!=', 'refund');
          })->orWhere('order_id', null);
        })->where('prize_id', 53)->get();
        foreach($user_gifts as $user_gift){
          $voucher = Voucher::where('code', $user_gift->code)->where('amount', '=', 1000)
              ->where('count', '>', 0)
              ->where(function ($query) {
                $query->where('available_from', '<', date('Y-m-d H:i:s'))
                    ->orWhere('available_from', null);
                return $query;
              })
              ->where(function ($query) {
                $query->where('available_until', '>', date('Y-m-d H:i:s'))
                    ->orWhere('available_until', null);
                return $query;
              })->first();
          if ($voucher){
            $certs[] = $voucher->code;
          }
        }
      }

      $giftOrder = null;
      $instaPromo = null;
      if(getSettings('happyCoupon')){
        $hpDate = (new GiftCoupon)->getDate();
        $instaPromo = $user->settings()->where('key', 'instaPromo')->first();
        if(!$instaPromo){
          $goldGifts = $user->giftCoupons()->where('data->position', '!=', null)->where('created_at', '>', $hpDate->format('Y-m-d H:i:s'))->count();
          if($goldGifts){
            $instaPromo = $user->settings()->create([
                'key' => 'instaPromo',
                'value' => 1
            ]);
          }
        }
        if($instaPromo && $instaPromo->value){ //
          $giftOrderId = $user
              ->giftCoupons()
              ->select('order_id')
              ->where('data->position', '!=', null)
              ->where('created_at', '>', $hpDate->format('Y-m-d H:i:s'))
              ->orderBy('created_at', 'desc')
              ->first();
          if($giftOrderId){
            $giftOrder = Order::find($giftOrderId->order_id)->slug;
          }

        }
      }
//      $wishesButton = $user->orders()->where('created_at', '>', '2024-12-25 10:00')->where('confirm', 1)->exists();
      $wishesButton = null;
      $user = auth()->user();
      $coupons = $user->coupons;
      $seo = [
          'title' => 'Личный кабинет'
      ];
      return view('template.cabinet.orders.index', compact('orders', 'seo', 'certs', 'user', 'giftOrder', 'instaPromo', 'wishesButton', 'coupons'));
    }

  public function show(Order $order){
    $user = auth()->user();
    if($user->id != $order->user_id){
      abort(403, 'У вас нет доступа к данной странице');
    }
    if(!isset($order->data['is_voucher'])||!$order->data['is_voucher']){


      $data_shipping = $order->data_shipping;
      $pickup = Pickup::where('code', $data_shipping['shipping-code'])->first();
      $data_shipping['info']['method'] = $data_shipping['shipping-method'] ?? '';
      $data_shipping['info']['code'] = $data_shipping['shipping-code'] ?? '';

      if($data_shipping['shipping-code'] == 'boxberry'){
        $data_shipping['info']['address_name'] = 'Адрес доставки';
        $data_shipping['info']['address'] = $order->data_shipping['boxberry-pvz-address'];
        $data_shipping['info']['track'] = $order->data_shipping['boxberry']['track'] ?? null;
        $data_shipping['info']['tracking_link'] = 'https://boxberry.ru/tracking-page?id='.$data_shipping['info']['track'];
      }elseif($order->data_shipping['shipping-code'] == 'yandex'){
        $data_shipping['info']['address_name'] = 'Адрес доставки';
        $data_shipping['info']['address'] = $order->data_shipping['yandex-pvz-address'];
        $data_shipping['info']['track'] = $order->data_shipping['yandex']['track'] ?? null;
        $data_shipping['info']['tracking_link'] = '#?id='.$data_shipping['info']['track'];
      }elseif($order->data_shipping['shipping-code'] == 'x5post'){
        $data_shipping['info']['address_name'] = 'Адрес доставки';
        $data_shipping['info']['address'] = $order->data_shipping['x5post-pvz-address'];
        $data_shipping['info']['track'] = $order->data_shipping['x5post']['senderOrderId'] ?? null;
        $data_shipping['info']['tracking_link'] = 'https://fivepost.ru/tracking/?id='.$data_shipping['info']['track'];
      }elseif($order->data_shipping['shipping-code'] == 'cdek'){
        $data_shipping['info']['address_name'] = 'Адрес доставки';
        $data_shipping['info']['address'] = ($order->data_shipping['cdek-pvz-address']);
        $data_shipping['info']['track'] = $order->data_shipping['cdek']['invoice_number'] ?? null;
        $data_shipping['info']['tracking_link'] = 'https://www.cdek.ru/ru/tracking?order_id='.$data_shipping['info']['track'];
      }elseif($order->data_shipping['shipping-code'] == 'cdek_courier'){
        $data_shipping['info']['address_name'] = 'Адрес доставки';
        $data_shipping['info']['address'] = $order->data_shipping['cdek_courier-form-address'];
        $data_shipping['info']['track'] = $order->data_shipping['cdek_courier']['invoice_number'] ?? null;
        $data_shipping['info']['tracking_link'] = 'https://www.cdek.ru/ru/tracking?order_id='.$data_shipping['info']['track'];
      }elseif($order->data_shipping['shipping-code'] == 'pochta'){
        $data_shipping['info']['address_name'] = 'Адрес доставки';
        $data_shipping['info']['address'] = $order->data_shipping['full_address'];
        $data_shipping['info']['track'] = $order->data_shipping['pochta']['barcode'] ?? null;
        $data_shipping['info']['tracking_link'] = 'https://www.pochta.ru/tracking?barcode='.$data_shipping['info']['track'];
      }elseif(isset($pickup->code)&&$order->data_shipping['shipping-code'] == $pickup->code){
        $data_shipping['info']['address_name'] = 'Самовывоз по адресу';
        $data_shipping['info']['address'] = $pickup->address.', <br/>Режим работы пункта выдачи: '.($pickup->params['times'] ?? '').'<br/>Телефон '.$pickup->phone;
      }

      $order->data_shipping = $data_shipping;
    }else{
      $pickup = null;
    }

    $cart = [];
    $gifts = [];
    foreach ($order->data_cart as $item) {
      if (isset($item['raffle'])) {
        $gifts[] = $item;
      } else {
        $cart[] = $item;
      }
    }
    $seo = [
        'title' => 'Заказ №'.$order->getOrderNumber()
    ];
    return view('template.cabinet.orders.order', compact('order', 'user', 'seo', 'pickup', 'cart', 'gifts'));
  }


  public function hideWindow($id)
  {
    $user = auth()->user();
    $setting = $user->settings()->where('id', $id)->first();
    if($setting){
      $setting->update([
          'value' => 0
      ]);
    }
    return back();
  }
}
