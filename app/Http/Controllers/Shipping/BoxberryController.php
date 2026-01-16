<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Controller;
use App\Jobs\getBoxberryTicketsJob;
use App\Models\BoxberryCity;
use App\Models\BoxberryPvz;
use App\Models\BoxberryRegion;
use App\Models\Order;
use App\Models\Product;
use App\Models\Region;
use App\Models\ShippingLog;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use App\Jobs\UpdateBoxberryCitiesJob;
use App\Jobs\UpdateBoxberryPvzsJob;
use App\Services\MailSender;
use App\Services\TelegramSender;
use App\Services\tfpdf\tFPDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Picqer\Barcode\BarcodeGeneratorJPG;

class BoxberryController extends Controller
{
  protected $yandex;
  public function __construct()
  {
    $this->yandex = new \App\Services\Boxberry\Client(300, 'https://api.boxberry.ru/json.php');
    $this->yandex->setToken('main', config('services.boxberry.apikey')); // Заносим токен BB и присваиваем ему ключ main
    $this->yandex->setCurrentToken('main');
  }

  public function updateCities($page = 0){
    $perPage = 50;
    $cities = $this->yandex->getCityList();
    $cities = array_slice($cities, $page * $perPage, $perPage);
    foreach($cities as $city){
      if ($city['CountryCode']!=643){
        continue;
      }
      $region_db = BoxberryRegion::where('name', '=', $city['Region'])->first();
      if (!$region_db){
        $main_region = Region::query()->where('name', 'like', '%'.$city['Region'].'%')->first();
        if(!$main_region){
          $main_region = Region::create([
              'name' => $city['Region'],
              'country_id' => 1,
          ]);
        }
        $region_db = BoxberryRegion::create([
            'name' => $city['Region'],
            'lm_country_id' => 1,
            'lm_region_id' => $main_region->id
        ]);
      }
      $city_db = BoxberryCity::where('Code', '=', $city['Code'])->first();
      if (!$city_db){
        BoxberryCity::create([
            'Name' => $city['Name'],
            'Code' => $city['Code'],
            'ReceptionLaP' => $city['ReceptionLaP'] ?? null,
            'DeliveryLaP' => $city['DeliveryLaP'] ?? null,
            'Reception' => $city['Reception'] ?? null,
            'ForeignReceptionReturns' => $city['ForeignReceptionReturns'] ?? null,
            'Terminal' => $city['Terminal'] ?? null,
            'CourierReception' => $city['CourierReception'] ?? null,
            'Kladr' => $city['Kladr'] ?? null,
            'Region' => $city['Region'] ?? null,
            'UniqName' => $city['UniqName'] ?? null,
            'District' => $city['District'] ?? null,
            'region_id' => $region_db->id,
        ]);
      }else{
        $city_db->update([
            'Name' => $city['Name'],
            'Code' => $city['Code'],
            'ReceptionLaP' => $city['ReceptionLaP'] ?? null,
            'DeliveryLaP' => $city['DeliveryLaP'] ?? null,
            'Reception' => $city['Reception'] ?? null,
            'ForeignReceptionReturns' => $city['ForeignReceptionReturns'] ?? null,
            'Terminal' => $city['Terminal'] ?? null,
            'CourierReception' => $city['CourierReception'] ?? null,
            'Kladr' => $city['Kladr'] ?? null,
            'Region' => $city['Region'] ?? null,
            'UniqName' => $city['UniqName'] ?? null,
            'District' => $city['District'] ?? null,
            'region_id' => $region_db->id,
        ]);
      }
    }
    if (count($cities) == $perPage) {
      $page++;
      UpdateBoxberryCitiesJob::dispatch($page)->onQueue('boxberry_city');
    }
  }
  public function updatePvzs($page = 0){
    $perPage = 500;
    $pvzs = $this->yandex->getPvzList(true);
    $pvzs = array_slice($pvzs, $page * $perPage, $perPage);
    foreach($pvzs as $pvz){
      $city = BoxberryCity::select('id', 'region_id')->where('Code', $pvz['CityCode'])->first();
      if (!$city){
        continue;
      }

      $pvz_db = BoxberryPvz::where('code', '=', $pvz['Code'])->first();
      if (!$pvz_db){
        $pvz_new = BoxberryPvz::create([
            'code' => $pvz['Code'],
            'name' => $pvz['Name'],
            'address' => $pvz['Address'],
            'phone' => $pvz['Phone'],
            'work_schedule' => $pvz['WorkShedule'],
            'trip_description' => $pvz['TripDescription'],
            'delivery_period' => $pvz['DeliveryPeriod'],
            'city_code' => $pvz['CityCode'],
            'city_name' => $pvz['CityName'],
            'tariff_zone' => $pvz['TariffZone'],
            'settlement' => $pvz['Settlement'],
            'area' => $pvz['Area'],
            'country' => $pvz['Country'],
            'only_prepaid_orders' => $pvz['OnlyPrepaidOrders'],
            'address_reduce' => $pvz['AddressReduce'],
            'acquiring' => $pvz['Acquiring'],
            'digital_signature' => $pvz['DigitalSignature'],
            'type_of_office' => $pvz['TypeOfOffice'],
            'nalKD' => $pvz['NalKD'],
            'metro' => $pvz['Metro'],
            'volume_limit' => $pvz['VolumeLimit'],
            'load_limit' => $pvz['LoadLimit'],
            'GPS' => $pvz['GPS'],
            'region_id' => $city->region->id ?? null,
            'city_id' => $city->id,
            'created_at' => now()->format('Y-m-d H:i'),
            'updated_at' => now()->format('Y-m-d H:i'),
        ]);
      }else{
        $pvz_db->update([
            'code' => $pvz['Code'],
            'name' => $pvz['Name'],
            'address' => $pvz['Address'],
            'phone' => $pvz['Phone'],
            'work_schedule' => $pvz['WorkShedule'],
            'trip_description' => $pvz['TripDescription'],
            'delivery_period' => $pvz['DeliveryPeriod'],
            'city_code' => $pvz['CityCode'],
            'city_name' => $pvz['CityName'],
            'tariff_zone' => $pvz['TariffZone'],
            'settlement' => $pvz['Settlement'],
            'area' => $pvz['Area'],
            'country' => $pvz['Country'],
            'only_prepaid_orders' => $pvz['OnlyPrepaidOrders'],
            'address_reduce' => $pvz['AddressReduce'],
            'acquiring' => $pvz['Acquiring'],
            'digital_signature' => $pvz['DigitalSignature'],
            'type_of_office' => $pvz['TypeOfOffice'],
            'nalKD' => $pvz['NalKD'],
            'metro' => $pvz['Metro'],
            'volume_limit' => $pvz['VolumeLimit'],
            'load_limit' => $pvz['LoadLimit'],
            'GPS' => $pvz['GPS'],
            'region_id' => $city->region->id ?? null,
            'city_id' => $city->id,
            'updated_at' => now()->format('Y-m-d H:i'),
        ]);
      }
    }
    if (count($pvzs) == $perPage) {
      $page++;
      UpdateBoxberryPvzsJob::dispatch($page)->onQueue('boxberry_pvz');
    }else{
      BoxberryPvz::query()->where('updated_at', '<', now()->format('Y-m-d'))->delete();
    }
  }
  public function getStatus($track){
    try {
      $res = $this->yandex->getOrderStatuses($track);
      /*
       Array
       (
           [0] => Array
               (
                   [Date] => 2019-05-01T00:56:12
                   [Name] => Принято к доставке
                   [Comment] =>
               )

           [1] => Array
               (
                   [Date] => 2019-05-01T00:56:13
                   [Name] => Передано на сортировку
                   [Comment] =>
               )

           [2] => Array
               (
                   [Date] => 2019-05-03T08:43:56
                   [Name] => Передан на доставку до пункта выдачи
                   [Comment] =>
               )

           [3] => Array
               (
                   [Date] => 2019-05-04T06:47:48
                   [Name] => Передан на доставку до пункта выдачи
                   [Comment] =>
               )

           [4] => Array
               (
                   [Date] => 2019-05-04T11:48:01
                   [Name] => Поступило в пункт выдачи
                   [Comment] => Москва (115478, Москва г, Каширское ш, д.24, строение 7)
               )

       )
      */
    }
    catch (\App\Services\Boxberry\Exception\BoxBerryException $e) {
      // Обработка ошибки вызова API BB
      // $e->getMessage(); текст ошибки
      // $e->getCode(); http код ответа сервиса BB
      // $e->getRawResponse(); // ответ сервера BB как есть (http request body)
    }
    catch (\Exception $e) {
      // Обработка исключения
    }
    return $res ?? [];
  }
  private function sendOrder($params = array()){

    try {
      $order = new \App\Services\Boxberry\Entity\Order();
      // $order->setTrackNum(TRACK_NUM); // Трекномер заказа в системе BB. Заполняется, если нужно изменить данные заказа
      //$order->setDeliveryDate('2019-05-10'); // Дата доставки от +1 день до +5 дней от текущий даты (только для доставки по Москве, МО и Санкт-Петербургу)
      $order->setOrderId($params['order_id']); // ID заказа в ИМ
      $order->setBarcode($params['order_id']);
      $order->setValuatedAmount($params['price']); // Объявленная стоимость
      $order->setPaymentAmount(0); // Сумма к оплате
      $order->setDeliveryAmount(0); // Стоимость доставки
      $order->setComment($params['comment']); // Комментарий к заказу
       $order->setVid(\App\Services\Boxberry\Entity\Order::PVZ); // Тип доставки (1 - ПВЗ, 2 - КД, 3 - Почта России)
       $order->setPvzCode($params['pvz_code']); // Код ПВЗ
      $order->setPointOfEntry("cec5d25c-fdf4-479d-baad-f88054025d6a");
//      $order->setVid(\App\Services\Boxberry\Entity\Order::COURIER); // Тип доставки (1 - ПВЗ, 2 - КД, 3 - Почта России)
//
      $customer = new \App\Services\Boxberry\Entity\Customer();
      $customer->setFio($params['fio']); // ФИО получателя
      $customer->setPhone($params['phone']); // Контактный номер телефона
      $customer->setEmail($params['email']); // E-mail для оповещений
//
//      $customer->setIndex(115551); // Почтовый индекс получателя (не заполянется, если в ПВЗ)
//      $customer->setCity('Москва'); // (не заполянется, если в ПВЗ)
//      $customer->setAddress('Москва, ул. Маршала Захарова, д. 3а кв. 1'); // Адрес доставки (не заполянется, если в ПВЗ)
//      $customer->setTimeFrom('10:00'); // Время доставки от
//      $customer->setTimeTo('18:00'); // Время доставки до
//      $customer->setTimeFromSecond('10:00'); // Альтернативное время доставки от
//      $customer->setTimeToSecond('18:00'); // Альтернативное время доставки до
//      $customer->setDeliveryTime('С 10 до 19, за час позвонить'); // Время доставки текстовый формат

      // Поля ниже заполняются для организации (не обязательные)
      //$customer->setOrgName('ООО Ромашка'); // Наименование организации
      //$customer->setOrgAddress('123456 Москва, Красная площадь дом 1'); // Арес организации
      //$customer->setOrgInn('7731347089'); // ИНН организации
      //$customer->setOrgKpp('773101001'); // КПП организации
      //$customer->setOrgRs('40702810500036265800'); // РС организации
      //$customer->setOrgKs('30101810400000000225'); // КС банка
      //$customer->setOrgBankName('ПАО Сбербанк'); // Наименование банка
      //$customer->setOrgBankBik('044525225'); // БИК банка

      $order->setCustomer($customer);

      // Создаем товары
      foreach($params['items'] as $params_item){
        $item = new \App\Services\Boxberry\Entity\Item();
        $item->setId($params_item['id']); // ID товара в БД ИМ'
        $item->setName($params_item['name']); // Название товара
        $item->setAmount($params_item['price']); // Цена единицы товара
        $item->setQuantity($params_item['quantity']); // Количество
        $item->setVat(0); // Ставка НДС
        $item->setUnit('шт'); // Единица измерения
        $order->setItems($item);
      }
      // Создаем места в заказе
      $place = new \App\Services\Boxberry\Entity\Place();
      $place->setWeight($params['weight']); // Вес места в граммах
      // $place->setBarcode('1234567890'); // ШК места
      $order->setPlaces($place);

      $order->setIssue(\App\Services\Boxberry\Entity\Order::TOI_DELIVERY_WITHOUT_OPENING); // вид выдачи (см. константы класса)

//      // Для отправления Почтой России необходимо заполнить дополнительные параметры
//      $russianPostParams = new \App\Services\Boxberry\Entity\RussianPostParams();
//      $russianPostParams->setType(\App\Services\Boxberry\Entity\RussianPostParams::PT_POSILKA); // Тип отправления (см. константы класса)
//      $russianPostParams->setFragile(true); // Хрупкая посылка
//      $russianPostParams->setStrong(true); // Строгий тип
//      $russianPostParams->setOptimize(true); // Оптимизация тарифа
//      $russianPostParams->setPackingType(\App\Services\Boxberry\Entity\RussianPostParams::PACKAGE_IM_MORE_160); // Тип упаковки (см. константы класса)
//      $russianPostParams->setPackingStrict(false); // Строгая упаковка
//
//      // Габариты тарного места (см) Обязательны для доставки Почтой России.
//      $russianPostParams->setLength(10);
//      $russianPostParams->setWidth(10);
//      $russianPostParams->setHeight(10);
//
//      $order->setRussianPostParams($russianPostParams);
      $result = $this->yandex->createOrder($order);

      /*
       array(
         'track'=>'DUD15224387', // Трек-номер BB
         'label'=>'URI' // Ссылка на скачивание PDF файла с этикетками
       );
       */
      return $result;
    }

    catch (\App\Services\Boxberry\Exception\BoxBerryException $e) {
      $result = ['error' => $e->getMessage().' номер заказа '.$params['order_id']];
      ShippingLog::create([
          'code' => 'yandex',
          'title' => 'Ошибка отправки заказа',
          'text' => $e->getMessage().' номер заказа '.$params['order_id'],
      ]);
      // Обработка ошибки вызова API BB
      // $e->getMessage(); текст ошибки
      // $e->getCode(); http код ответа сервиса BB
      // $e->getRawResponse(); // ответ сервера BB как есть (http request body)
    }

    catch (\Exception $e) {
      ShippingLog::create([
          'code' => 'yandex',
          'title' => 'Ошибка отправки заказа',
          'text' => $e->getMessage().' номер заказа '.$params['order_id'],
      ]);
      $result = ['error' => $e->getMessage().' номер заказа '.$params['order_id']];
    }
    return $result;
  }
  public function prepareOrdersToBoxberry($order_ids, $user_id)
  {
    $orders = Order::whereIn('id', $order_ids)->get();
    foreach ($orders as $order) {
      $data = $order->data;
      $data_cart = $order->data_cart;
      $data_shipping = $order->data_shipping;
      if (!isset($data_shipping['yandex-pvz-id']) || $data_shipping['yandex-pvz-id'] === null) {
        echo $order->id.'<br/>';
        continue;
      }
      $order_number = $order->getOrderNumber() . '_';
      $total = 0;
      $weight = 0;
      $order_items = [];
      $error = false;
      foreach ($data_cart as $item) {
        $product = Product::query()->where('id', $item['id'])->select('price')->first();
        if (isset($item['raffle'])&&!$product->price){
          continue;
        }
        $total += $item['price'] * $item['qty'];

        $order_items[] = [
            'id' => $item['id'], // id товара
            'name' => $item['name'] . ' (' . $item['model'] . ')', // наименование товара
            'price' => $item['price'], // Цена единицы товара
            'quantity' => $item['qty'], // Количество товара
        ];

        $product = Product::select('weight')->where('id', $item['id'])->first();
        if (!is_numeric($product->weight)){
          ShippingLog::create([
              'code' => 'yandex',
              'title' => 'Найдена ошибка в заказе '.$order->id,
              'text' => 'Не указан вес у товара «'.$item['name'].'»',
          ]);
          $order->setStatus('is_processing');
          $error = true;
          continue;
        }
        $weight += $product->weight;
      }
      if ($error){
        continue;
      }
      $order_number = (string)$order->getOrderNumber();
      if (isset($data['form']['full_name'])) {
        $full_name = $data['form']['full_name'];
      } else {
        $full_name = $data['form']['last_name'] . ' ' . $data['form']['first_name'];
        if (isset($data['form']['middle_name']) && !empty($data['form']['middle_name'])) {
          $full_name .= ' ' . $data['form']['middle_name'];
        }
      }
      $params_order = [
          'order_id' => $order_number, // ID заказа в ИМ
          'fio' => $full_name, // ID заказа в ИМ
          'phone' => $data['form']['phone'], // ID заказа в ИМ
          'email' => $data['form']['email'], // ID заказа в ИМ
          'price' => $total, // Объявленная стоимость
          'comment' => $order_number, // Комментарий к заказу
          'pvz_code' => $data_shipping['yandex-pvz-id'], // Код ПВЗ
          'weight' => $weight,
          'items' => $order_items
      ];

      $new_order = $this->sendOrder($params_order);

      if (isset($new_order['error'])) {
        $order->setStatus('is_processing');
        continue;
      }
//      if (!isset($new_order['label'])) {
//        return ['error' => 'Boxberry не создал этикетку для заказа ' . $order->getOrderNumber()];
//      }
      $data_shipping['yandex'] = $new_order;

      $order->update([
          'data_shipping' => $data_shipping
      ]);
      // email notify
      if (isset($new_order['track']) && $order->user->is_subscribed_to_marketing){
        (new MailSender($data['form']['email']))->trakingMessage($order);
        foreach($order->user->tgChats as $tgChat){
          (new TelegramSender($tgChat))->trakingMessage($order);
        }
      }
    }
    getBoxberryTicketsJob::dispatch($order_ids, $user_id)->delay(now()->addMinutes(5))->onQueue('boxberry_tickets');
    ShippingLog::create([
        'code' => 'yandex',
        'title' => denum($orders->count(), ['%d заказ','%d заказа','%d заказов']).' обработано',
        'text' => 'Заказы '.implode(', ', $orders->pluck('id')->toArray()),
    ]);
  }
  public function getTickets($order_ids = null, $user_id){
    $orders = Order::select()
        ->doesntHave('tickets')
        ->where(function($query){
          $query->whereIn('status', ['yandex_Заказ включен в акт. В обработке', 'yandex_загружен реестр им', 'yandex_заказ создан в личном кабинете', 'payment', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled']);
          $query->orWhere('status', null);
        })
        ->where('confirm', 1)
        ->where('data_shipping->ticket', null)
        ->whereIn('data_shipping->shipping-code', ['yandex'])
        ->where(function ($query) {
          $query->where('data_shipping->yandex->track', '!=', null);
        });
    if($order_ids){
      $orders->whereIn('id', $order_ids);
    }
    $orders = $orders->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();
    if ($orders->count()){
      $this->createBoxberryTicket($orders, $user_id);
      // getCdekTicketsJob::dispatch(0)->onQueue('cdek_tickets');
    }
  }

  public function checkStatus(Order $order){
    $data_shipping = $order->data_shipping;
    if ($data_shipping['shipping-code'] == 'yandex' && isset($data_shipping['yandex']['track']) && $order->status != 'refund') {
      // получаем статус заказа
      $yandex_track = $data_shipping['yandex']['track'];
      $yandex_status = $this->getStatus($yandex_track);

      if (is_array($yandex_status) && !empty($yandex_status)) {
        $this_status = $yandex_status[count($yandex_status) - 1];
        $this_status_name = trim($this_status['Name']);
        $db_status = Status::where('key', 'yandex_' . mb_strtolower($this_status_name))->first();
        if (!$db_status){
          Status::create([
              'key' => mb_substr('yandex_' . $this_status_name, 0, 255),
              'name' => mb_substr('Yandex: ' . $this_status_name, 0, 255),
              'color' => 'warning'
          ]);
        }
        $order->setStatus('yandex_' . $this_status_name);
      }
      return $yandex_status;
    }
    return false;
  }
  private function createBoxberryTicket($orders, $user_id)
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
        $invoice_number = $order->data_shipping['yandex']['track'] ?? null;
        $pvz_code = $order->data_shipping['yandex-pvz-id'];
        if (!$invoice_number){
          continue;
        }
        $recipient_city = '';
        if($pvz_code){
          $yandex_pvz = BoxberryPvz::where('code', $pvz_code)->first();
          if ($yandex_pvz) {
            $recipient_city = $yandex_pvz->address;
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
        if (!file_exists(public_path() . '/files/yandex/barcodes')) {
          mkdir(public_path() . '/files/yandex/barcodes', 0777, true);
        }
        $barcode_path = public_path() . '/files/yandex/barcodes/'.$order->id.'.jpg';
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
        $pdf->Cell((239.94-20)/2, 14, 'Яндекс Доставка', 0, 0, 'R');


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
            if($item['id'] > 1000){
              $item['id'] -= 1000;
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
//              $builder_models .= 'm' . $builder_item['product_id'] . '-' . $builder_item['qty'].', ';
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
      $directory = '/files/yandex_1/tickets';

      if (!file_exists(public_path() . $directory)) {
        mkdir(public_path() . $directory, 0777, true);
      }
      $result = $pdf->Output('F', public_path() . $directory . '/' . $file_name);

      $ticket = Ticket::create([
          'file_name' => $file_name,
          'file_path' => $directory . '/' . $file_name,
          'items_count' => $orders->count(),
          'delivery_code' => 'yandex',
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
}
