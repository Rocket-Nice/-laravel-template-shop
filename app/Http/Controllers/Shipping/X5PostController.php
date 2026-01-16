<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Jobs\getX5PostTicketsJob;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingLog;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use App\Models\X5PostCity;
use App\Models\X5PostPvz;
use App\Models\X5PostRegion;
use App\Jobs\UpdateX5PostPvzJob;
use App\Services\tfpdf\tFPDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Picqer\Barcode\BarcodeGeneratorJPG;

class X5PostController extends Controller
{
    private $client;
    public function __construct()
    {
      try {
        $jwtHelper = new \LapayGroup\FivePostSdk\Helpers\JwtSaveFileHelper();
        $this->client = new \LapayGroup\FivePostSdk\Client('f1aebbaf-f2fe-4262-84be-3ff7d22b932d', 60, \LapayGroup\FivePostSdk\Client::API_URI_PROD, $jwtHelper);
        $jwt = $this->client->getJwt();
      }
      catch (\LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        Log::error($e->getCode().': '.$e->getMessage());
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); текст ошибки
        // $e->getCode(); http код ответа сервиса 5post
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
      }
      catch (\Exception $e) {
        Log::error($e->getCode().': '.$e->getMessage());
        // Обработка исключения
      }
    }

    public function updatePvz($page = 0){
      $result = $this->client->getPvzList($page, 1000); // Больше 2000 за раз получить нельзя
      foreach($result['content'] as $pvz_data){
        $region_params = [
            'name' => $pvz_data['address']['region'] ?? null,
            'region_type' => $pvz_data['address']['regionType'] ?? null
        ];
        $region = $this->regionFindOrCreate($region_params);
        if(!$region && ($pvz_data['address']['region'] ?? null)){
          Log::debug('регион '.$pvz_data['address']['region'].' не добавлен');
          continue;
        }
        $city_params = [
            'region_id' => $region->id ?? null,
            'name' => $pvz_data['address']['city'] ?? null,
            'city_type' => $pvz_data['address']['cityType'] ?? null
        ];
        $city = $this->cityFindOrCreate($city_params);
        if(!$city){
          Log::debug('город '.$pvz_data['address']['city'].' не добавлен');
          continue;
        }
        $pvz_params = [
            'region_id' => $region->id ?? null,
            'city_id' => $city->id,
            'pvz_id' => $pvz_data['id'],
            'mdmCode' => $pvz_data['mdmCode'] ?? null,
            'name' => $pvz_data['name'] ?? null,
            'partnerName' => $pvz_data['partnerName'] ?? null,
            'multiplaceDeliveryAllowed' => $pvz_data['multiplaceDeliveryAllowed'] ?? null,
            'type' => $pvz_data['type'] ?? null,
            'country' => $pvz_data['address']['country'] ?? null,
            'fullAddress' => $pvz_data['fullAddress'] ?? null,
            'shortAddress' => $pvz_data['shortAddress'] ?? null,
            'address_lat' => $pvz_data['address']['lat'] ?? null,
            'address_lng' => $pvz_data['address']['lng'] ?? null,
            'additional' => $pvz_data['additional'] ?? null,
            'cellLimits' => $pvz_data['cellLimits'] ?? null,
            'returnAllowed' => $pvz_data['returnAllowed'] ?? null,
            'timezoneOffset' => $pvz_data['timezoneOffset'] ?? null,
            'phone' => $pvz_data['phone'] ?? null,
            'cashAllowed' => $pvz_data['cashAllowed'] ?? null,
            'cardAllowed' => $pvz_data['cardAllowed'] ?? null,
            'loyaltyAllowed' => $pvz_data['loyaltyAllowed'] ?? null,
            'extStatus' => $pvz_data['extStatus'] ?? null,
            'rate' => $pvz_data['rate'] ?? null,
            'createDate' => isset($pvz_data['createDate']) ? Carbon::parse($pvz_data['createDate'])->format('Y-m-d H:i:s') : null,
            'openDate' =>  isset($pvz_data['openDate']) ? Carbon::parse($pvz_data['openDate'])->format('Y-m-d H:i:s') : null,
            'timezone' => $pvz_data['timezone'] ?? null,
            'outsideX5' => $pvz_data['outsideX5'] ?? null,
            'is_active' => true,
        ];
        $pvz = $this->pvzUpdateOrCreate($pvz_params);
      }
      if (!empty($result['totalPages']) && $result['totalPages'] > $page) {
        UpdateX5PostPvzJob::dispatch($page+1)->onQueue('x5post_pvzs');
      }else{
        DB::update('UPDATE `x5_post_pvzs` SET `is_active`=0 WHERE `updated_at` < "' . now()->subDay()->format('Y-m-d') . '";');
      }
    }

    private function regionFindOrCreate($region_params){
      if(!($region_params['name'] ?? null)){
        return null;
      }
      $region_name = $region_params['name'];
      $region_name = preg_replace('/обл\.$/', 'область', $region_name);
      $region_name = preg_replace('/обл$/', 'область', $region_name);
      $region_params['name'] = trim($region_name);
      $region = X5PostRegion::query()
          ->where('name', $region_name)
          ->first();
      if(!$region){
        $region = X5PostRegion::create($region_params);
      }
      return $region;
    }

    private function cityFindOrCreate($city_params){
      if(!($city_params['name'] ?? null)){
        return null;
      }
      $city = X5PostCity::query()
          ->where('region_id', $city_params['region_id'])
          ->where('name', $city_params['name'])
          ->first();
      if(!$city){
        $city = X5PostCity::create($city_params);
      }
      return $city;
    }

    private function pvzUpdateOrCreate($pvz_parmas){
      $pvz = X5PostPvz::query()
          ->where('pvz_id', $pvz_parmas['pvz_id'])
          ->first();
      if(!$pvz){
        $pvz_parmas['created_at'] = now()->format('Y-m-d H:i');
        $pvz_parmas['updated_at'] = now()->format('Y-m-d H:i');
        $pvz = X5PostPvz::create($pvz_parmas);
      }else{
        $pvz_parmas['updated_at'] = now()->format('Y-m-d H:i');
        $pvz = $pvz->update($pvz_parmas);
      }
      return $pvz;
    }

    public function create_warehouse()
    {
      $warehouse = new \LapayGroup\FivePostSdk\Entity\Warehouse();
      $warehouse->setId('lemousse1');
      $warehouse->setName('Склад ИП Нечаева');
      $warehouse->setCountryId('RU');
      $warehouse->setRegionCode(34);
      $warehouse->setFederalDistrict('Южный');
      $warehouse->setRegion('Волгоград');
      $warehouse->setZipCode(111024);
      $warehouse->setCity('Москва');
      $warehouse->setStreet('ул Социалистическая');
      $warehouse->setHouse('57');
      $warehouse->setLatitude('48.692392');
      $warehouse->setLongitude('44.482390');
      $warehouse->setPhone('+79376990623');
      $warehouse->setTimeZone('+03:00');

      $workhours = [
          [
              'dayNumber' => 1,
              'timeFrom' => '09:00:00',
              'timeTill' => '18:00:00',
          ],
          [
              'dayNumber' => 2,
              'timeFrom' => '09:00:00',
              'timeTill' => '18:00:00',
          ],
          [
              'dayNumber' => 3,
              'timeFrom' => '09:00:00',
              'timeTill' => '18:00:00',
          ],
          [
              'dayNumber' => 4,
              'timeFrom' => '09:00:00',
              'timeTill' => '18:00:00',
          ],
          [
              'dayNumber' => 5,
              'timeFrom' => '09:00:00',
              'timeTill' => '18:00:00',
          ],
      ];
//      for ($i = 1; $i < 6; $i++) {
//        $workDay = new \LapayGroup\FivePostSdk\Entity\WorkingDay();
//        $workDay->setDay($i); // 1 - понедельник, 7 - воскресенье
//        $workDay->setTimeFrom('09:00:00');
//        $workDay->setTimeTill('18:00:00');
//        $warehouse->setWorkingDay($workDay);
//      }
//      dd($warehouse->asArr());
      $warehouse_parmas = $warehouse->asArr();
      $warehouse_parmas['workingTime'] = $workhours;
      $result = $this->client->addWarehouses([$warehouse_parmas]);
      dd($result);
    }

    public function prepareOrders($order_ids, $user_id){
      $orders = Order::whereIn('id', $order_ids)->get();
      $result = $this->createOrder($orders);
      if ($result !== false && $result) {
        getX5PostTicketsJob::dispatch($order_ids, $user_id)->delay(now()->addMinutes(5))->onQueue('x5post_tickets');
        ShippingLog::create([
            'code' => 'x5post',
            'title' => denum($result->count(), ['%d заказ','%d заказа','%d заказов']).' обработано',
            'text' => 'Заказы '.implode(', ', $result->pluck('id')->toArray()),
        ]);
      }
      return true;
    }

  public function createOrder($orders)
  {
    $x5post_orders = [];
    foreach($orders as $key => $order){
      $data = $order->data;
      $data_cart = $order->data_cart;
      $data_shipping = $order->data_shipping;
      $order_number = $order->getOrderNumber();
      $pvz = X5PostPvz::query()->where('mdmCode', $data_shipping['x5post-pvz-id'])->first();
      $x5post_order = new \LapayGroup\FivePostSdk\Entity\Order();
      $x5post_order->setWarehouseId('lemousse1');
      $x5post_order->setId($order_number);
      $x5post_order->setNumber($order_number);
      $x5post_order->setCompanyName(config('app.name'));
      $x5post_order->setFio($data['form']['full_name']);
      $x5post_order->setPhone($data['form']['phone']);
      $x5post_order->setEmail($data['form']['email']);
      $x5post_order->setPaymentValue(0);
      $x5post_order->setPaymentCur('RUB');
      $x5post_order->setPaymentType(\LapayGroup\FivePostSdk\Entity\Order::P_TYPE_PREPAYMENT);
      $x5post_order->setPrice($data['total']);
      $x5post_order->setPriceCur('RUB');
      $x5post_order->setPvzId($pvz->pvz_id);
      $x5post_order->setUndeliverableOption(\LapayGroup\FivePostSdk\Entity\Order::UNDELIVERED_RETURN);

      $Place = new \LapayGroup\FivePostSdk\Entity\Place();
      $Place->setBarcode($order_number);
      $Place->setId($order_number);
      $Place->setPrice($data['total']);
      $Place->setVatRate(20);
      $Place->setCurrency('RUB');
      $Place->setHeight(120);
      $Place->setLength(110);
      $Place->setWidth(210);

      $total = 0;
      $weight = 0;
      foreach($data_cart as $item){
        $product = Product::query()->select('price', 'weight')->where('id', $item['id'])->first();
        if (isset($item['raffle'])&&!$product->price){
          continue;
        }
        $total += $product->price * $item['qty'];
        $weight += $product->weight;

        $x5post_item = new \LapayGroup\FivePostSdk\Entity\Item();
//        $item->setBarcode('32270000000001');
        $x5post_item->setQuantity((int)$item['qty']);
//        $item->setCodeGtg('1020911016032000003592');
        $x5post_item->setName($item['name']);
        $x5post_item->setPrice((int)$item['price']);
        $x5post_item->setCurrency('RUB');
        $x5post_item->setVatRate(20);
        $x5post_item->setArticul($item['model']);
        $Place->setItem($x5post_item);
      }
      $Place->setWeight($weight*1000);
      $x5post_order->setPlace($Place);
      $x5post_orders[] = $x5post_order;
    }

    try{
      $result = $this->client->createOrders($x5post_orders);
//      Log::debug(print_r($result, true));
      $created_ids = [];
      foreach($result as $sentOrder){
        if($sentOrder['errors'] ?? null){
          foreach($sentOrder['errors'] as $error){
            ShippingLog::create([
                'code' => 'x5post',
                'title' => 'Ошибка при отправке заказа '.$sentOrder['senderOrderId'],
                'text' => $error['message']
            ]);
          }
        }
        if(!$sentOrder['created']){
          continue;
        }
        $order = $orders->where('id', substr($sentOrder['senderOrderId'], 2))->first();
        $created_ids[] = $order->id;
        $data_shipping = $order->data_shipping;
        $data_shipping['x5post'] = $sentOrder;
        $order->update([
            'data_shipping' => $data_shipping
        ]);
        $order->setStatus('was_processed');
      }
    }catch(\Exception $exception){
      $partnerOrders = [];
      foreach ($x5post_orders as $order) {
        $partnerOrders[] = $order->asArr();
      }
      Log::error("Ошибка запроса при отгрузке заказов x5post\n\$x5post_orders ".print_r($partnerOrders, true));
      ShippingLog::create([
          'code' => 'x5post',
          'title' => 'Ошибка запроса при отгрузке заказов',
          'text' => $exception->getMessage(),
      ]);
      return false;
    }
    $orders = Order::query()->whereIn('id', $created_ids)->get();
    return $orders;
  }

  public function getTickets($order_ids = [], $user_id){
    $orders = Order::select()
        ->doesntHave('tickets')
        ->where(function($query){
          $query->whereIn('status', ['payment', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled', 'x5post_approved']);
          $query->orWhere('status', null);
        })
        ->where('confirm', 1)
        ->where('data_shipping->ticket', null)
        ->whereIn('data_shipping->shipping-code', ['x5post'])
        ->where(function ($query) {
          $query->where('data_shipping->x5post->created', '!=', null);
        });
    if($order_ids && !empty($order_ids)){
      $orders->whereIn('id', $order_ids);
    }
    $orders = $orders->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();
    if ($orders->count()){
      $this->createTicket($orders, $user_id);
    }
  }

  private function createTicket($orders, $user_id)
  {
    $user = User::find($user_id);
    // формируем группы по 50 заказов
    $orders_ids = implode(',', $orders->pluck('id')->toArray());

    $ticket_count = DB::select('SELECT COUNT(*) as count FROM `order_ticket` WHERE `order_id` IN (?);', [$orders_ids]);
    $ticket_count = $ticket_count[0]->count;
    $creator = null;

    if ($ticket_count == 0 || true) {
      $pdf = new tFPDF('P', 'pt', [239.94, 297.64]);
      $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
      $pdf->AddFont('DejaVu','B','DejaVuSansCondensed-Bold.ttf',true);


      foreach($orders as $order){
        $user = auth()->user();
        // генерация баркода
        $data_cart = $order->data_cart;

        $order_number = (string)$order->getOrderNumber();
        //
        $invoice_number = $order->data_shipping['x5post']['senderOrderId'] ?? null;
        $pvz_code = $order->data_shipping['x5post-pvz-id'];
        if (!$invoice_number){
          continue;
        }
        $recipient_city = '';
        if($pvz_code){
          $x5post_pvz = X5PostPvz::where('mdmCode', $pvz_code)->first();
          if ($x5post_pvz) {
            $recipient_city = $x5post_pvz->fullAddress ?? $x5post_pvz->shortAddress;
          }
        }
        $recipient_name = $order->data['form']['full_name'];
        $recipient_phone = $order->data['form']['phone'];
        $sender_name = config('app.name');
        $sender_city = 'Волгоград';
        $cart = $order->data_cart;

        $generator = new BarcodeGeneratorJPG();
        $barcode = $generator->getBarcode($order_number, $generator::TYPE_CODE_128, 2, 30);
        //$barcode = $generator->getBarcode($order->getOrderNumber(), $generator::TYPE_CODE_128, 2, 30);
        if (!file_exists(public_path() . '/files/x5post/barcodes')) {
          mkdir(public_path() . '/files/x5post/barcodes', 0777, true);
        }
        $barcode_path = public_path() . '/files/x5post/barcodes/'.$order->id.'.jpg';
        file_put_contents($barcode_path, $barcode);
        $image = Image::make($barcode_path);
        $width = $image->getWidth();
        $image->resize($width, (int)($width*0.15));
        $image->save($barcode_path);
        $pdf->AddPage();
        $pdf->SetFont('DejaVu','',8);
        $pdf->Image($barcode_path, 10, 10, 239.94-20);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 20);
        //$pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY(10, 50);
        $pdf->Cell((239.94-20)/3, 14, $invoice_number, 0, 0, 'L');

        // $pdf->SetFont('DejaVu','B',14);
        // $pdf->Cell((239.94-20)/2, 14, $order->getOrderNumber(), 0, 0, 'R');
        $pdf->SetFont('DejaVu','',8);
        $pdf->Cell((239.94-20)/3*2, 14, $order_number, 0, 0, 'R');
        $pdf->SetXY(10, 65);
        $pdf->SetFont('DejaVu','B',8);
        $pdf->Cell((239.94-20)/2, 14, 'Получатель', 0, 0, 'L');
        $pdf->SetFont('DejaVu','',8);
        $pdf->Cell((239.94-20)/2, 14, date('d.m.Y H:i'), 0, 0, 'R');
        $pdf->SetXY(10, 80);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(0, 14, "$recipient_name ($recipient_phone)\n$recipient_city", 1, 'L', true);
        $pdf->SetFont('DejaVu','B',8);
        $pdf->Cell(0, 14, 'Отправитель', 0, 0, 'L');
        $pdf->Ln(15);
        $pdf->setX(10);
        $pdf->SetFont('DejaVu','',8);
        $pdf->MultiCell(0, 14, "$sender_name\n$sender_city", 1, 'L', true);
//        $pdf->SetFont('DejaVu','',10);
//        $pdf->MultiCell(0, 14, "Le Mousse", 0, 'R', true);

        $pdf->SetFont('DejaVu','B',8);
        $pdf->Cell((239.94-20)/2, 14, 'Место 1 из 1', 0, 0, 'L');
        $pdf->SetFont('DejaVu','',10);
        $pdf->Cell((239.94-20)/2, 14, '5Пост', 0, 0, 'R');


        $pdf->Ln(20);
        $pdf->SetFont('DejaVu','',10);
        $pdf->Cell(0, 14, 'Заказ '.$order->id, 0, 0, 'L');
        $pdf->Ln(20);
        $pdf->SetFont('DejaVu','',11);
        $i = 0;
        $cart_chunk = array_chunk(mergeItemsById($cart), 5);
        $item_code = '';
        $has_builder = false;
        foreach($cart_chunk as $cart_chunk_item){

          foreach ($cart_chunk_item as $item) {
            $product = Product::query()->where('id', $item['id'])->select('price')->first();
            if (isset($item['raffle'])&&!$product->price){
              continue;
            }
            $item_code .= 'm' . $item['id'] . '-' . $item['qty'].', ';
          }
        }
        $pdf->MultiCell(0, 14, "$item_code", 0, 'L', true);

//        if($has_builder){
//          foreach($cart as $item){
//            if(!isset($item['builder'])){
//              continue;
//            }
//            $pdf->AddPage();
//            $pdf->SetFont('DejaVu','',8);
//            $pdf->SetMargins(10, 10, 10);
//            $pdf->SetAutoPageBreak(true, 20);
//            //$pdf->SetFont('Arial', 'B', 9);
//            $pdf->SetXY(10, 20);
//            $pdf->SetFont('DejaVu','B',8);
//            $pdf->Cell(0, 14, $order_number, 0, 0, 'L');
//            $pdf->SetFont('DejaVu','',8);
//
//            $pdf->SetFont('DejaVu','',8);
//            $pdf->setY(35);
//            $pdf->SetFillColor(255,255,255);
//            $pdf->MultiCell(0, 14, $item['name'].' ('.$item['model'].' '.$item['qty'].' шт)', 1, 'L', true);
//            $builder_models = '';
//            $builder_text = '';
//            foreach($item['builder'] as $builder_item){
//              $builder_models .= 'k' . $builder_item['product_id'] . '-' . $builder_item['qty'].', ';
//              $builder_text .= "– {$builder_item['name']} - {$builder_item['qty']}шт,\n";
//            }
//            $pdf->SetFont('DejaVu','',6);
//            $pdf->SetFillColor(255,255,255);
//            $pdf->setY(70);
//            $pdf->MultiCell(0, 14, $builder_text."\n".$builder_models, 1, 'L', true);
//          }
//        }
      }
      $file_name = Str::random(8).'_'.($creator ?? $user->email ?? '_').'.pdf';
      $directory = '/files/x5post/tickets';

      if (!file_exists(public_path() . $directory)) {
        mkdir(public_path() . $directory, 0777, true);
      }
      $result = $pdf->Output('F', public_path() . $directory . '/' . $file_name);

      $ticket = Ticket::create([
          'file_name' => $file_name,
          'file_path' => $directory . '/' . $file_name,
          'items_count' => $orders->count(),
          'delivery_code' => 'x5post',
      ]);
      foreach ($orders as $order) {
        if ($order->tickets()->count() > 0) { // если дубль, то удаляем наклейки и запускаем отмену
          $cancel = true;
          Log::debug('Ошибка ticket заказ ' . $order->getOrderNumber());
          break;
        }
        $order->tickets()->attach($ticket->id);
        $data_shipping = $order->data_shipping;
        $data_shipping['ticket'] = $directory . '/' . $file_name;
        $order->update([
            'data_shipping' => $data_shipping
        ]);
      }
      if (isset($cancel) && $cancel) {
        foreach ($orders as $order) {
          $order->tickets()->detach($ticket->id);
        }
      }
    }
  }

  public function checkStatuses($orders)
  {
    $x5postOrders = [];
    foreach($orders as $order){
      $data_shipping = $order->data_shipping;
      if($data_shipping['shipping-code'] != 'x5post' || !isset($data_shipping['x5post']['orderId'])){
        continue;
      }
      $x5postOrders[] = $data_shipping['x5post']['orderId'];
    }
    $result = $this->client->getOrdersStatusByVendorId($x5postOrders);

    foreach ($result as $status) {
      // Проверка на конечный статусы
      if (\LapayGroup\FivePostSdk\Enum\OrderStatus::isFinal($status['executionStatus'])) {
        // TODO  логика обработки конечного статуса, после которого запрос статусов не требуется
      }

      // Получение текстового описания статуса
      $status_text   = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['status']);
//      $exstatus_text = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['executionStatus']);
      $status_code = 'x5post_' . mb_strtolower($status['status']);
      if($status_text){
        $db_status = Status::where('key', $status_code)->first();
        if (!$db_status){
          Status::create([
              'key' => $status_code,
              'name' => '5 Пост: ' . $status_text,
              'color' => 'warning'
          ]);
          Status::flushQueryCache();
        }
        $status_date = ($status['changeDate'] ?? null) ? Carbon::parse($status['changeDate']) : now();
        $order_id = Order::getOrderId($status['senderOrderId']);
        $order = Order::find($order_id);
        $order->setStatus($status_code, $status_date);
      }else{
        Log::debug('5 Пост статус не определен '.print_r($status,true));
      }
    }
    return true;
  }
}
