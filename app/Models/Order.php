<?php

namespace App\Models;

use App\Models\Traits\FilterOrder;
use App\Models\Traits\Loggable;
use App\Services\TelegramSender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, Loggable, FilterOrder;

  public function coupon(){
    return $this->hasMany('App\Models\Coupone', 'owner_order_id', 'id');
  }
  public const PRIZRS = [157,174,248];

    private $number_prefix = 'LE';

    static public function getOrderId($number){
      return substr($number, 2);
    }

    public function getOrderNumber($just_num = false, $html = false){
      if (isset($this->id)){
        if ($just_num){
          return $this->id;
        }else{
          if($this->id>288576){
            $this->number_prefix = 'LM';
          }
          if($html){
            return $this->number_prefix.'<span class="cormorantInfant">'.$this->id.'</span>';
          }else{
            return $this->number_prefix.$this->id;
          }
        }
      }
    }

    public function user() {
      return $this->belongsTo('App\Models\User');
    }

    public function city() {
      return $this->belongsTo('App\Models\City');
    }

  public function giftCoupons() {
    return $this->hasMany('App\Models\GiftCoupon');
  }

    public function coupones() {
      return $this->hasMany('App\Models\RaffleMember');
    }
  public function promocode() {
    return $this->hasOne('App\Models\Coupone');
  }
  public function voucher() {
    return $this->hasOne('App\Models\Voucher');
  }
  public function storeCoupon() {
    return $this->hasOne('App\Models\StoreCoupon');
  }
    public function tickets() {
      return $this->belongsToMany('App\Models\Ticket', 'order_ticket');
    }
    public function invoices() {
      return $this->belongsToMany('App\Models\Invoice', 'order_invoice');
    }
    public function items(){
      return $this->hasMany(OrderItem::class);
    }
    public function cdek_orders() {
      return $this->hasMany('App\Models\CdekOrder');
    }
  public function status_history()
  {
    return $this->hasMany('App\Models\OrderStatusHistory');
  }

    public function couponesLimit() {
      $limit = 0;
      if ($this->data['total'] >= 2500&&$this->data['total'] < 5000){
        $limit = 1;
      }elseif($this->data['total'] >= 5000){
        $limit = 3;
      }
      return $limit;
    }

    public function canAddReview(){
      if(!$this->created_at){
        return false;
      }
      $created_at = Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at);
      if($this->confirm && $created_at->lessThan(now()->subWeeks(2))){
        return true;
      }
      return false;
    }


  public function getStatus($status_code = null)
  {
    $status = Status::where('key', '=', $status_code ?? $this->status)->first();
    return $status;
  }

  public function getStatusText($class = null)
  {
    $status_code = $this->status;
    if (!$status_code && !$this->confirm) {
      return 'Не оплачен';
    }elseif(!$status_code){
      return 'В обработке';
    }
    $status = Status::where('key', '=', $status_code)->first();
    return $status->name;
  }
  public function getStatusBadge($class = null)
  {
    $status_code = $this->status;
    if (!$status_code && !$this->confirm) {
      return '<span class="badge-gray whitespace-nowrap opacity-80 '.$class.'">Не оплачен</span>';
    }elseif(!$status_code){
      return '<span class="badge-gray whitespace-nowrap '.$class.'">В обработке</span>';
    }
    $status = Status::where('key', '=', $status_code)->first();
    return '<span class="badge-' . getBootstrapColor($status->color ?? 'secondary') . ' whitespace-nowrap '.$class.'">' . ($status->name ?? $status_code) . '</span>';
  }
  public function setStatus($status)
  {
    if($this->status == $status){
      return false;
    }
    if($status == 'cancelled'){
      $this->cancelOrder();
    }
    $this->update([
        'status' => $status,
        'status_updated_at' => now()->format('Y-m-d H:i:s')
    ]);

    $this->addStatusHistory($status);

    return true;
  }

  private function cancelOrder(){
    $data_cart = $this->data_cart;
    $data_shipping = $this->data_shipping;
    $cart_ids = [];
    $cart_qty = [];
    foreach($data_cart as $cart_item){
      $orderItem = OrderItem::setParams($this->id, $cart_item);
      $cart_ids[] = $cart_item['id'];
      $cart_qty[$cart_item['id']] = $cart_item['qty'];
      if(isset($cart_item['parent_product'])){
        $cart_ids[] = $cart_item['parent_product'];
        $cart_qty[$cart_item['parent_product']] = $cart_item['qty'];
      }
    }
    $pickup = Pickup::select('code', 'name', 'params')->where('code', $data_shipping['shipping-code'])->first();
    $products = Product::whereIn('id', $cart_ids)->get();
    foreach ($products as $product) {
      if (isset($pickup)&&$pickup&&isset($pickup->params['quantity'])){ // &&$product->id!=69){
        $quantity_field = 'data_quantity->'.$pickup->params['quantity'];
        DB::table('products')->where('products.id', $product->id)->increment($quantity_field, $cart_qty[$product->id]);
        if($product->product_id){
          DB::table('products')->where('products.id', $product->product_id)->increment($quantity_field, $cart_qty[$product->id]);
        }
      }else{
        DB::update('UPDATE `products` SET `quantity`=`quantity`+'.$cart_qty[$product->id].' WHERE `id` = ' . $product->id . ';');
        if($product->product_id){
          DB::update('UPDATE `products` SET `quantity`=`quantity`+'.$cart_qty[$product->id].' WHERE `id` = ' . $product->product_id . ';');
        }
      }
    }
    Product::flushQueryCache();
    $order_data = $this->data;
    $order_shipping = $this->data_shipping;
    // возвращаем промокод или подарочный сертификат
    $discount = $order_data['discount'] ?? 0;

    if ($discount > 0 && $order_data['total'] + $order_shipping['price'] - $this->amount == $discount) {

      if(isset($order_data['voucher'])){
        $voucher = $this->voucher()->where('code', $order_data['voucher']['code'])->first();
        if($voucher){
          $voucher->update([
              'order_id' => null,
              'used_at' => null,
          ]);
        }
      }elseif(isset($order_data['promocode'])){
        $promocode = $this->promocode()->where('code', $order_data['promocode']['code'])->first();
        if($promocode){
          $promocode->update([
              'order_id' => null,
              'used_at' => null,
          ]);
        }
      }elseif(isset($order_data['bonuses'])&&$order_data['bonuses']&&$order_data['discount'] > 0){
        $user = $this->user;
        // проверяем, не использовали ли супер бонусы
        $super_bonus_transaction = $user->super_bonus_transactions()->where('amount', '<', 0)->where('comment', 'like', '%'.$this->id)->first();
        if($super_bonus_transaction){
          $super_bonuses_amount = $super_bonus_transaction->amount*-1;
          $user->addSuperBonuses($super_bonuses_amount, 'Аннулирован заказ '.$this->id);
          if($order_data['discount']-$super_bonuses_amount > 0){
            $user->addBonuses($order_data['discount']-$super_bonuses_amount, 'Аннулирован заказ '.$this->id);
          }

        }else{
          $user->addBonuses($order_data['discount'], 'Аннулирован заказ '.$this->id);
        }

      }
    }
    return true;
  }

  public function addStatusHistory($status){
    $last_status = $this->status_history()->orderBy('created_at', 'desc')->first();
    if (!$last_status || $last_status->status != $status) {
      OrderStatusHistory::create([
          'order_id' => $this->id,
          'status' => $status,
          'created_at' => now()->format('Y-m-d H:i:s'),
          'updated_at' => now()->format('Y-m-d H:i:s'),
      ]);
      // уведомление о смене статуса
      $tgChats = $this->user->tgChats;

      if(strpos($status, 'cdek') !== false || strpos($status, 'boxberry') !== false || strpos($status, 'cdek_courier') !== false) {
        $statusEntity = Status::where('key', '=', $status)->first();
        foreach($tgChats as $tgChat){
          (new TelegramSender($tgChat))->customMessage("Статус вашего заказа #".$this->getOrderNumber()." изменен на ".$statusEntity->name);
        }
      }
    }
  }
    public function getRouteKeyName()
    {
      return 'slug';
    }

  public function getCoupones(){
    return $this
        ->coupones()
        // ->where('prize_id', '!=', null)
        //->where('created_at', '<', date('Y-m-d H:i:s', strtotime('-60 minutes')))
        ->where('created_at', '>', '2023-03-01');
  }

    protected $casts = [
        'data' => 'array',
        'data_cart' => 'array',
        'data_payment' => 'array',
        'data_shipping' => 'array',
        'data_kkt' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'data',
        'data_cart',
        'data_shipping',
        'data_payment',
        'data_kkt',
        'amount',
        'confirm',
        'slug',
        'payment_provider',
        'prizes',
        'partner_id',
      'status',
      'status_updated_at',
      'country_id',
      'region_id',
      'city_id',
    ];
}
