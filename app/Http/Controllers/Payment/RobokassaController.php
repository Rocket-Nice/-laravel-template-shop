<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Jobs\CheckRobokassaPaymentsJob;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RobokassaController extends Controller
{
  // бой
    private $login;
    private $pass1;
    private $pass2;
    private $test = 0;

    public function __construct(){
      $this->test = getSettings('payment_test');
      $this->pass1 = config('services.robokassa.pass1');
      $this->pass2 = config('services.robokassa.pass2');
      $this->login = config('services.robokassa.login');
      if(getSettings('payment_test')){
        $this->pass1 = config('services.robokassa.test1'); // test
        $this->pass2 = config('services.robokassa.test2'); // test
      }
    }

    public function index(Order $order){
//      if(auth()->check() && auth()->id()==1){
//        dd($this->pass1, $this->pass2);
//      }
      if ($order->confirm == 1) {
        return redirect('/order/success?InvId='.$order->id);
      }
      if($order->status=='cancelled'){
        abort(403, 'Данный заказ аннулирован');
      }
      $order_data = $order->data;
      $order_data['cart'] = $order->data_cart;
      $order_shipping = $order->data_shipping;

      $user = $order->user;
      // проверяме телефон
//      $phone = $user->phones()->where('number', $order_data['form']['phone'])->first();
//      if (!$phone){
//        $phone = Phone::create([
//            'number' => $order_data['form']['phone'],
//            'user_id' => $user->id
//        ]);
//      }
//      if (!$phone->confirmed){
//        return redirect()->route('order.phone.page', ['order' => $order->slug, 'number' => $phone->number])->with(['warning' => 'Подтвердите ваш телефон']);
//      }

      $cart = Cart::instance('cart');
      $order_info = [
          'total' => $order_data['total'],
          'amount' => $order->amount,
          'full_name' => $order_data['form']['full_name'],
          'email' => $order_data['form']['email'],
          'telephone' => $order_data['form']['phone'],
          'shipping' => $order_shipping,
      ];
      $recepient = $this->getReceiptData($order_info, $order_data);
      if (isset($recepient['error'])) {
        return redirect()->route('order.create')->withErrors([
            $recepient['error']
        ]);
      }
      $order->update([
          'data_kkt' => $recepient
      ]);
      // ИП Максим
      $params = array(
          'mrh_login' => $this->login,
          'mrh_pass1' => $this->pass1,
          'inv_id' => $order->getOrderNumber(true),
          'inv_desc' => 'Косметическая продукция «le mousse»',
          'out_summ' => $order->amount,
          'Email' => $order->data['form']['email'],
          'encoding' => "utf-8",
          'test' => $this->test,
          'Receipt' => json_encode($recepient,JSON_UNESCAPED_UNICODE)
      );

      $crc = md5($params['mrh_login'] . ':' . $params['out_summ'] . ':' . $params['inv_id'] . ':' . $params['Receipt'] . ':' . $params['mrh_pass1']);
      $params['crc'] = $crc;
      $cart->destroy();

//      if(!isset($order->data['this_status']['status'])){
//        $new_status = [
//            'status' => 'payment',
//            'date' => date('Y-m-d H:i:s')
//        ];
//        (new \App\Http\Controllers\Admin\OrderController())->setStatus($order, $new_status);
//      }

      return view('template.public.order.robokassa', compact('order', 'params'));
    }

  private function getReceiptData($order_info, $order_data)
  {
    $amount_total = $order_info['amount'];
    $receiptData = array(
        'items' => array(),
        'sno' => 'osn'
    );

    if(env('IP')=='ovchinnikova'){
      $receiptData['sno'] = 'osn';
    }
    $discount = 0;
    if ((!isset($order_data['promocode']['discount_cart']) || empty($order_data['promocode']['discount_cart'])) && isset($order_data['discount'])) {
      $discount = $order_data['total'] + ($order_info['shipping']['price'] ?? 0) - $amount_total;
    }else{
      $discount = $order_data['total'] + ($order_info['shipping']['price'] ?? 0) - $amount_total;
      if ($discount<=0){
        $discount = 0;
      }
    }
    $cart = $order_data['cart'];

    foreach ($cart as $item) {
      if($item['price'] == 0){
        continue;
      }
      // проверяем наличие
      // проверяем наличие
      $product = Product::dontCache()->select(
          'id',
          'quantity',
          'status',
          'options',
          'data_status',
          'data_quantity',
          'product_options'
      )->where('id', $item['id'])->first();
      // собираем корзину
//      if (isset($order_data['promocode']['discount_cart'][$item['model']])) { // проверяем сидку на товар
//        $discount_item = $order_data['promocode']['discount_cart'][$item['model']];
//        if ($discount_item['qty'] == $item['qty']) { // если весь товар со скидкой
//          $amount = $discount_item['price'] * $discount_item['qty'];
//          $order_amount = $order_amount + $amount;
//        } else {
//          $item = array(
//              'name' => trim($item['name'] . ' ' . $item['model']),
//              'quantity' => $item['qty'],
//              'sum' => $discount_item['price'] * $discount_item['qty'],
//              'payment_method' => 'full_payment',
//              'payment_object' => 'commodity',
//              'tax' => 'none'
//          );
//          $receiptData['items'][] = $item;
//          $amount = $item['price'] * ($item['qty'] - $discount_item['qty']);
//          $order_amount = $order_amount + $amount;
//        }
//      } else {
//        $amount = $item['price'] * $item['qty'];
//        $order_amount = $order_amount + $amount;
//      }
      $amount = $item['price'] * $item['qty'];
      if ($discount > 0) {
        $amount -= $discount;
        if ($amount < 0) {
          $amount = 0;
        }
        $discount -= $item['price'] * $item['qty'];
      }
      $item = array(
          'name' => trim($item['name'] . ' ' . $item['model']),
          'quantity' => $item['qty'],
          'sum' => $amount,
          'payment_method' => 'full_payment',
          'payment_object' => 'commodity',
          'tax' => 'vat20'
      );
      if(env('IP')=='ovchinnikova'){
        $item['tax'] = 'vat20';
      }
      $receiptData['items'][] = $item;
    }

    $shipping_price = $order_info['shipping']['price'];
    if ($discount > 0) {
      $shipping_price -= $discount;
      if ($shipping_price < 0) {
        $shipping_price = 0;
      }
      $discount -= $order_info['shipping']['price'];
    }
    // Order Totals
    if ($order_info['shipping']['price'] > 0) {
      $item = array(
          'name' => 'доставка',
          'quantity' => 1,
          'sum' => $shipping_price,
          'payment_method' => 'full_payment',
          'payment_object' => 'service',
          'tax' => 'vat20'
      );
      if(env('IP')=='ovchinnikova'){
        $item['tax'] = 'vat20';
      }
      $receiptData['items'][] = $item;

    }

    return $receiptData;
  }

  public function check(Request $request){
    $request_params = $request->toArray();
    $mrh_pass2 = $this->pass2;

    $tm = getdate(time() + 9 * 3600);
    $date = "$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";

    $order = Order::find($request->InvId);
    if ($order&&$order->confirm == 1){
      return 'OK'.$order->getOrderNumber(true);
    }
    $params = array(
        'OutSum' => $request->OutSum,
        'InvId' => $request->InvId,
        'SignatureValue' => $request->SignatureValue,
    );

    $out_summ = $request->OutSum;
    $inv_id = $request->InvId;
    $crc = $request->SignatureValue;

    $crc = strtoupper($crc);

    $my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2"));

    $confrim = 1;
    if ($my_crc != $crc) {
      Log::error('robokassa check'.print_r($request_params, true));
      die();
    }

    if ($order) {
      $order->update([
          'amount' => $request->OutSum,
          'data_payment' => $request->toArray(),
          'confirm' => 1,
          'payment_provider' => 'robokassa',
      ]);
    } else {
      Log::debug('Robokassa платеж не найден ' . print_r($request_params, true));
      return json_encode(array('code' => 0));
    }
    $user = User::find($order->user_id);

    (new OrderController)->finishOrder($user, $order);
    return 'OK'.$order->getOrderNumber(true);
  }

  public function checkPayments($page = 1, $queue = true, $limit = 10){
      /*
       * State->Code
        Код текущего состояния операции оплаты счета.
        Возможные значения:
        5
        – Операция только инициализирована, деньги от покупателя не получены. Или от пользователя ещё не поступила оплата по выставленному ему счёту или платёжная система, через которую пользователь совершает оплату, ещё не подтвердила факт оплаты.
        10
        – Операция отменена, деньги от покупателя не были получены. Оплата не была произведена. Покупатель отказался от оплаты или не совершил платеж, и операция отменилась по истечении времени ожидания. Либо платёж был совершён после истечения времени ожидания. В случае возникновения спорных моментов по запросу от продавца или покупателя, операция будет перепроверена службой поддержки, и в зависимости от результата может быть переведена в другое состояние.
        50
        – Деньги от покупателя получены, производится зачисление денег на счет магазина. Операция перешла в состояние зачисления средств на баланс продавца. В этом статусе платёж может задержаться на некоторое время. Если платёж «висит» в этом состоянии уже долго (более 20 минут), это значит, что возникла проблема с зачислением средств продавцу.
        60
        – Деньги после получения были возвращены покупателю. Полученные от покупателя средства возвращены на его счёт (кошелёк), с которого совершалась оплата.
        80
        – Исполнение операции приостановлено. Внештатная остановка. Произошла внештатная ситуация в процессе совершения операции (недоступны платежные интерфейсы в системе, из которой/в которую совершался платёж и т.д.) Или операция была приостановлена системой безопасности. Операции, находящиеся в этом состоянии, разбираются нашей службой поддержки в ручном режиме.
        100
        Платёж проведён успешно, деньги зачислены на баланс продавца, уведомление об успешном платеже отправлено
       */
    $orders = Order::select()
        ->where(function($query){
          $query->where('data_payment->Result->Code', null);
          $query->orWhere('data_payment->Result->Code', '!=', 0);
        })
        ->where('data->manual_mode', null)
        ->where('status', null)
        ->where('confirm', 0)
        ->where('created_at', '<', now()->subMinutes(30))
        ->paginate($limit, ['*'], 'page', $page);
    foreach($orders as $order){
      $crc = md5($this->login . ':' . $order->id . ':' . $this->pass2);
      $res = simplexml_load_file('https://auth.robokassa.ru/Merchant/WebService/Service.asmx/OpState?MerchantLogin='.$this->login.'&InvoiceID='.$order->id.'&Signature='.$crc, "SimpleXMLElement", LIBXML_NOCDATA);
      $res = json_decode(json_encode($res), true);
      if($res['Result']['Code']!=0||!isset($res['State']['Code'])){
        $createdAt = Carbon::parse($order->created_at);
        if ($createdAt->lt(Carbon::now()->subHour())) {
          $order->setStatus('cancelled');
        }
        $order->update(['data_payment' => $res]);
        continue;
      }
      if (in_array($res['State']['Code'], [10,60])) {
        $order->setStatus('cancelled');
        $order->update(['data_payment' => $res]);
      }elseif($res['State']['Code']==100) {
        $order->update([
            'amount' => $res['Info']['OutSum'],
            'data_payment' => $res,
            'confirm' => 1
        ]);
        (new OrderController)->finishOrder($order->user, $order);
      }
    }
    if ($orders->lastPage()>1 && $page < $orders->lastPage() && $queue){
      CheckRobokassaPaymentsJob::dispatch($page+1)->onQueue('robokassa_payments');
    }
    return true;
  }
}
