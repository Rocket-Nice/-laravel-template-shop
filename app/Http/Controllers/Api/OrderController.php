<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CdekOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function getCart(Request $request)
    {
      header('Access-Control-Allow-Origin: *');
      header("Content-type: application/json; charset=utf-8");
      $data = $request->toArray();
      $rs = file_get_contents('php://input');
      $m = $_SERVER['REQUEST_METHOD'];
      //Log::debug('query');
      if(empty($rs) || $m === 'GET'){
        header("HTTP/1.1 500 Your web server may erroneously change POST requests into GET (thus discarding POST data) when a redirect is issued. This should be visible in the server's access log.");
        exit(1);
      }

      $r = json_decode($rs, true);
//      Log::debug('query');
//      Log::debug($rs);
//      Log::debug(print_r($r, true));
      $data = $r;
      // Log::debug(print_r($request->toArray(), true));
      $answer = [];
      foreach($data as $item){
        if($item['barcode']=='Демо'){
          Log::debug(print_r($item, true));
          $answer[] = array(
              'result_id' => $item['id'],
              'result_title' => trim('Принят демо запрос',', '),
              'result_color' => 3,
              'result_report' => trim('Принят демо запрос',', '),
          );
          continue;
        }
        if (strpos($item['barcode'], "LM") === 0) { // boxberry
          $order = Order::where('id', str_replace('LM', '', $item['barcode']))->first();
          if(!$order){
            $answer[] = array(
                'result_id' => $item['id'],
                'result_title' => 'Заказ "'.$item['barcode'].'" не найден',
                'result_color' => 3,
                'result_report' => 'Заказ "'.$item['barcode'].'" не найден',
                'result_icon' => 1
            );
          }
        } else { // cdek
          $barcode = $item['barcode'];
          $order = Order::query()->where(function ($query) use($barcode) {
            $query->where('data_shipping->cdek->invoice_number', '=', $barcode);
            $query->orWhere('data_shipping->cdek_courier->invoice_number', '=', $barcode);
          })->first();
          if (!$order){
            $answer[] = array(
                'result_id' => $item['id'],
                'result_title' => 'Заказ "'.$item['barcode'].'" не найден',
                'result_color' => 3,
                'result_report' => 'Заказ "'.$item['barcode'].'" не найден',
                'result_icon' => 1
            );
            continue;
          }
        }

        $data = $order->data;
        $data_cart = $order->data_cart;
        $text_cart = '';
        foreach($data_cart as $cart_item){
          $text_cart .= 'm' . $cart_item['id'] . '-' . $cart_item['qty'].', ';
        }
        $answer[] = array(
            'result_id' => $item['id'],
            'result_title' => trim($text_cart,', '),
            'result_color' => 3,
            'result_report' => trim($text_cart,', '),
        );
        $order->setStatus('is_assembled');
      }

      echo json_encode($answer);
    }

    public function by_user(Request $request){
      $request->validate([
          'user_id' => ['required', 'exists:users,id']
      ]);
      $orders = Order::query()
          ->where('user_id', $request->user_id)
          ->where('confirm', 1)
          ->get(['id', 'user_id', 'data', 'data_cart', 'data_shipping', 'created_at', 'amount'])
          ->map(function ($order) {
            $data_cart = $order->data_cart;
            $cart = [];
            foreach($data_cart as $item){
              $cart[] = [
                  'product_id' => $item['id'],
                  'qty' => $item['qty'],
                  'name' => $item['name'],
                  'price' => $item['price'],
              ];
            }
            return [
                'order_number' => $order->getOrderNumber(),
                'user_id' => $order->user_id,
                'cart' => $cart,
                'cart_total' => (int)$order->data['total'],
                'shipping_price' => (int)$order->data_shipping['price'] ?? 0,
                'amount' => $order->amount,
                'created_at' => \Carbon\Carbon::create($order->created_at)->format('d.m.Y H:i:s'),
            ];
          });
      return response()->json($orders);
    }
}
