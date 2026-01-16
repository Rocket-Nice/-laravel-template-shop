<?php

namespace App\Http\Controllers\Shipping;

use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\Shipping\ShippingLogController;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingLog;
use App\Models\Ticket;
use App\Notifications\MailDeliveryNotification;
use App\Notifications\MailNotification;
use App\Services\MailSender;
use App\Services\TelegramSender;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log as Log;
use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Facades\Notification;
use LapayGroup\RussianPost\Entity\CustomsDeclaration;
use LapayGroup\RussianPost\Entity\CustomsDeclarationItem;
use LapayGroup\RussianPost\ParcelInfo;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Entity\Order as PochtaOrder;
use LapayGroup\RussianPost\Providers\Tracking;
use Symfony\Component\Yaml\Yaml;

class PochtaController extends Controller
{
    private $client;
    private $authParams;
    public function __construct()
    {
      $otpravkaAuth['token'] = 'TaiLfmgxyncQZGjT2oTRnlRBjapJppnj';
      $otpravkaAuth['key']   = 'ZG9zdGF2a2FfaXBfYm9yaXNvdmFAbWFpbC5ydTpVcFpfZEZnU006cjNfekQ=';

//      $trackingAuth['login'] = '89376990623';
//      $trackingAuth['password'] = 'Lemousse';
      $trackingAuth['login'] = 'yChwbHfxtrzbcc';
      $trackingAuth['password'] = 'XWT8ksTdVhUZ';

      $authParams['auth']['otpravka'] = $otpravkaAuth;
      $authParams['auth']['tracking'] = $trackingAuth;
      $this->authParams = $authParams;
      try {
        $this->client = new OtpravkaApi($authParams);
      }
      catch (\InvalidArgumentException $e) {
        // Обработка ошибки заполнения параметров
        Log::debug($e->getMessage().' string 28');
      }
      catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
        // Обработка ошибочного ответа от API ПРФ
        Log::debug($e->getMessage().' string 32');
      }
      catch (\Exception $e) {
        // Обработка нештатной ситуации
        Log::debug($e->getMessage().' string 36');
      }

    }

    public function tracking()
    {
      $Tracking = new Tracking('single', $this->authParams);
      $result = $Tracking->getOperationsByRpo('10944022440321');
      dd($result);
    }

    public function listPVZ(){
      $list = $this->client->shippingPoints();
      return $list;
    }

    public function calculate($postcode, $weight){
      $price = 0;
      try {
        $parcelInfo = new ParcelInfo();
        $parcelInfo->setIndexFrom(400961); // Индекс пункта сдачи из функции $OtpravkaApi->shippingPoints()
        $parcelInfo->setIndexTo($postcode);
        $parcelInfo->setMailCategory('ORDINARY'); // https://otpravka.pochta.ru/specification#/enums-base-mail-category
        $parcelInfo->setMailType('POSTAL_PARCEL'); // https://otpravka.pochta.ru/specification#/enums-base-mail-type
        $parcelInfo->setWeight($weight);
        $parcelInfo->setFragile(true);

        $tariffInfo = $this->client->getDeliveryTariff($parcelInfo);

        $price = $tariffInfo->getTotalRate()/100;

        /*
         LapayGroup\RussianPost\TariffInfo Object
         (
             [totalRate:LapayGroup\RussianPost\TariffInfo:private] => 30658
             [totalNds:LapayGroup\RussianPost\TariffInfo:private] => 6132
             [aviaRate:LapayGroup\RussianPost\TariffInfo:private] => 0
             [aviaNds:LapayGroup\RussianPost\TariffInfo:private] => 0
             [deliveryMinDays:LapayGroup\RussianPost\TariffInfo:private] => 1
             [deliveryMaxDays:LapayGroup\RussianPost\TariffInfo:private] => 3
             [fragileRate:LapayGroup\RussianPost\TariffInfo:p rivate] => 7075
             [fragileNds:LapayGroup\RussianPost\TariffInfo:private] => 1415
             [groundRate:LapayGroup\RussianPost\TariffInfo:private] => 30658
             [groundNds:LapayGroup\RussianPost\TariffInfo:private] => 6132
             [insuranceRate:LapayGroup\RussianPost\TariffInfo:private] => 0
             [insuranceNds:LapayGroup\RussianPost\TariffInfo:private] => 0
             [noticeRate:LapayGroup\RussianPost\TariffInfo:private] => 0
             [noticeNds:LapayGroup\RussianPost\TariffInfo:private] => 0
             [oversizeRate:LapayGroup\RussianPost\TariffInfo:private] => 0
             [oversizeNds:LapayGroup\RussianPost\TariffInfo:private] => 0
         )
         */
      }
      catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
        // Обработка ошибочного ответа от API ПРФ
        Log::debug('Обработка ошибочного ответа от API ПРФ: '.$e->getMessage());
      }
      catch (\Exception $e) {
        // Обработка нештатной ситуации
        Log::debug('Обработка ошибочного ответа от API ПРФ: '.$e->getMessage());
      }

      return $price;
    }
    public function prepareOrdersToPochta($order_ids, $user_id){
      $orders = Order::whereIn('id', $order_ids)->get();
      $result = $this->createOrder($orders);
      if ($result !== false && $result) {
        ShippingLog::create([
            'code' => 'pochta',
            'title' => denum($result->count(), ['%d заказ','%d заказа','%d заказов']).' обработано',
            'text' => 'Заказы '.implode(', ', $result->pluck('id')->toArray()),
        ]);
      }
      return true;
    }
    public function createOrder($orders)
    {
      $otpravkaAuth['token'] = 'RM5bk5Ib8kdLAmM8YOrs8dyIn4GGuDOg';
      $otpravkaAuth['key']   = 'ODkzNzY5OTA2MjM6TGVtb3Vzc2U=';

      $trackingAuth['login'] = '89376990623';
      $trackingAuth['password'] = 'Lemousse';

      $authParams['auth']['otpravka'] = $otpravkaAuth;
      $authParams['auth']['tracking'] = $trackingAuth;

      $orders_count = $orders->count();

      $pochta_orders = [];
      foreach($orders as $key => $order){
        $data = $order->data;
        $data_cart = $order->data_cart;

        $data_shipping = $order->data_shipping;
        try {
          $order_in_pochta = $this->client->findOrderByShopId($order->getOrderNumber(true));
          if (!empty($order_in_pochta)){
            $order_in_pochta = $order_in_pochta[0];
            if (!isset($order_in_pochta['barcode'])){
              dd($order_in_pochta);
            }
            $data_shipping['pochta'] = [
                'barcode' => $order_in_pochta['barcode'],
                'order-num' => $order_in_pochta['order-num'],
                'result-id' => $order_in_pochta['id']
            ];
            $order->update([
                'data_shipping' => $data_shipping
            ]);
            $order->setStatus('was_processed');
            (new MailSender($data['form']['email']))->trakingMessage($order);
            foreach($order->user->tgChats as $tgChat){
              (new TelegramSender($tgChat))->trakingMessage($order);
            }
            unset($orders[$key]);
            continue;
          }
        }
        catch (\InvalidArgumentException $e) {
          // Обработка ошибки заполнения параметров
          // $error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка заполнения параметров '.$order->id,
              'text' => $e->getMessage(),
          ]);
          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
          // Обработка ошибочного ответа от API ПРФ
          ///$error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка от API ПРФ. Заказ '.$order->id,
              'text' => $e->getMessage(),
          ]);
          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        catch (\Exception $e) {
          // Обработка нештатной ситуации
          //$error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка при отправке заказа '.$order->id,
              'text' => $e->getMessage(),
          ]);
          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }

        $country = Country::where('options->pochta_code', $data_shipping['country_code'])->first();
        if (!$country){
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка при отправке заказа '.$order->id,
              'text' => 'Не найдена страна доставки',
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        $country_options = $country->options;
        if (isset($country_options['pochta_code'])){
          $country_code = $country_options['pochta_code'];
        }else{
          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        $pochta_order = new PochtaOrder();
        try {
          // информация о заказе
          $pochta_order->setOrderNum($order->getOrderNumber(true));
          $pochta_order->setGivenName($data['form']['first_name']);
          $pochta_order->setSurname($data['form']['last_name']);
          $pochta_order->setMiddleName($data['form']['middle_name']);
          $pochta_order->setRecipientName($data['form']['full_name']);
          $pochta_order->setTelAddress($data['form']['phone']);
          $pochta_order->setSmsNoticeRecipient(1);

          // информация об отправке
          $pochta_order->setMailCategory('ORDINARY'); // категория РПО: Обыкновенное
          $pochta_order->setTransportType('SURFACE'); // Наземный
        }
        catch (\InvalidArgumentException $e) {
          // Обработка ошибки заполнения параметров
          // $error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка заполнения параметров '.$order->id,
              'text' => $e->getMessage(),
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
          // Обработка ошибочного ответа от API ПРФ
          ///$error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка от API ПРФ. Заказ '.$order->id,
              'text' => $e->getMessage(),
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        catch (\Exception $e) {
          // Обработка нештатной ситуации
          //$error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка при отправке заказа '.$order->id,
              'text' => $e->getMessage(),
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }

        // собираем адрес

        if ($data_shipping['country_code'] == '643'){ // россия
          try{
            $pochta_order->setMailType('ONLINE_PARCEL');
          }
          catch (\InvalidArgumentException $e) {
            // Обработка ошибки заполнения параметров
            // $error = $e->getMessage();
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка заполнения параметров '.$order->id,
                'text' => $e->getMessage(),
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }
          catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
            // Обработка ошибочного ответа от API ПРФ
            ///$error = $e->getMessage();
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка от API ПРФ. Заказ '.$order->id,
                'text' => $e->getMessage(),
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }
          catch (\Exception $e) {
            // Обработка нештатной ситуации
            //$error = $e->getMessage();
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка при отправке заказа '.$order->id,
                'text' => $e->getMessage(),
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }
          $addressList = new \LapayGroup\RussianPost\AddressList();
          $addressList->add($data_shipping['full_address']);
          $pochta_address = $this->client->clearAddress($addressList);
          // dd($pochta_address);
          if (!isset($pochta_address[0]['quality-code'])||
              !isset($pochta_address[0]['validation-code'])||
              !in_array($pochta_address[0]['quality-code'], ['GOOD', 'POSTAL_BOX', 'ON_DEMAND', 'UNDEF_05'])||
              !in_array($pochta_address[0]['validation-code'], ['VALIDATED', 'OVERRIDDEN', 'CONFIRMED_MANUALLY'])){
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка при отправке заказа '.$order->id,
                'text' => 'Невозможно определить адрес в заказе',
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }
          try {
            $pochta_order->setIndexTo($pochta_address[0]['index']);
          }
          catch (\InvalidArgumentException $e) {
            // Обработка ошибки заполнения параметров
            // $error = $e->getMessage();
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка заполнения параметров '.$order->id,
                'text' => $e->getMessage(),
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }
          catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
            // Обработка ошибочного ответа от API ПРФ
            ///$error = $e->getMessage();
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка от API ПРФ. Заказ '.$order->id,
                'text' => $e->getMessage(),
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }
          catch (\Exception $e) {
            // Обработка нештатной ситуации
            //$error = $e->getMessage();
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка при отправке заказа '.$order->id,
                'text' => $e->getMessage(),
            ]);

          $order->setStatus('is_processing');
            unset($orders[$key]);
            continue;
          }


          $total_weight = 0;
          $comment = '';

          foreach($data_cart as $cart_item){
            $product = Product::query()->select('weight', 'price')->where('id', $cart_item['id'])->first();
            if (isset($cart_item['raffle'])&&!$product->price){
              continue;
            }
            if (!is_numeric($product->weight)){
              ShippingLog::create([
                  'code' => 'pochta',
                  'title' => 'Найдена ошибка в заказе '.$order->id,
                  'text' => 'Не указан вес у товара «'.$cart_item['name'].'»',
              ]);

          $order->setStatus('is_processing');
              unset($orders[$key]);
              continue;
            }
            $comment .= 'm'.$cart_item['id'].'-'.$cart_item['qty'].' ';
            $weight = $product->weight;
            $total_weight += $weight * $cart_item['qty'];
          }
        }else{ // международаня
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка',
              'text' => 'Междунадродная доставка почтой не настроена',
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        // собираем корзину
        $comment = '';
        $data_cart = mergeItemsById($data_cart);
        foreach($data_cart as $cart_item){
          $product = Product::query()->where('id', $cart_item['id'])->select('price')->first();
          if (isset($cart_item['raffle'])&&!$product->price){
            continue;
          }
          $comment .= 'm'.$cart_item['id'].'-'.$cart_item['qty'].' ';

        }
        $comment = trim($comment, ', ');
        Log::debug($comment);
        try {
          $pochta_order->setComment($comment);
          $pochta_order->setMass($total_weight);

          $pochta_order->setPostOfficeCode(400961);
          $pochta_order->setCompulsoryPayment(0);
          if (isset($pochta_address[0]['area'])){
            $pochta_order->setAreaTo($pochta_address[0]['area']);
          }
          if (isset($pochta_address[0]['building'])){
            $pochta_order->setBuildingTo($pochta_address[0]['building']);
          }
          if (isset($pochta_address[0]['hotel'])){
            $pochta_order->setHotelTo($pochta_address[0]['hotel']);
          }
          if (isset($pochta_address[0]['letter'])){
            $pochta_order->setLetterTo($pochta_address[0]['letter']);
          }
          if (isset($pochta_address[0]['location'])){
            $pochta_order->setLocationTo($pochta_address[0]['location']);
          }
          if (isset($pochta_address[0]['num-address-type'])){
            $pochta_order->setNumAddressTypeTo($pochta_address[0]['num-address-type']);
          }
          if (isset($pochta_address[0]['office'])){
            $pochta_order->setOfficeTo($pochta_address[0]['office']);
          }
          if (isset($pochta_address[0]['slash'])){
            $pochta_order->setSlashTo($pochta_address[0]['slash']);
          }
          if (isset($pochta_address[0]['vladenie'])){
            $pochta_order->setVladenieTo($pochta_address[0]['vladenie']);
          }
          if (isset($pochta_address[0]['house'])){
            $pochta_order->setHouseTo($pochta_address[0]['house']);
          }else{
            $pochta_order->setHouseTo($data_shipping['house']);
          }
          if (isset($pochta_address[0]['corpus'])){
            $pochta_order->setCorpusTo($pochta_address[0]['corpus']);
          }
          if (isset($pochta_address[0]['corpus'])){
            $pochta_order->setCorpusTo($pochta_address[0]['corpus']);
          }
          if (isset($pochta_address[0]['place'])){
            $pochta_order->setPlaceTo($pochta_address[0]['place']);
          }else{
            $pochta_order->setPlaceTo($data_shipping['city']);
          }
          if (isset($pochta_address[0]['region'])){
            $pochta_order->setRegionTo($pochta_address[0]['region']);
          }
          if (isset($pochta_address[0]['street'])){
            $pochta_order->setStreetTo($pochta_address[0]['street']);
          }else{
            $pochta_order->setStreetTo($data_shipping['street']);
          }
          if (isset($pochta_address[0]['room'])){
            $pochta_order->setRoomTo($pochta_address[0]['room']);
          }else{
            $pochta_order->setRoomTo($data_shipping['flat']);
          }
          $pochta_orders[] = $pochta_order->asArr();
        }
        catch (\InvalidArgumentException $e) {
          // Обработка ошибки заполнения параметров
          // $error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка заполнения параметров '.$order->id,
              'text' => $e->getMessage(),
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
          // Обработка ошибочного ответа от API ПРФ
          ///$error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка от API ПРФ. Заказ '.$order->id,
              'text' => $e->getMessage(),
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
        catch (\Exception $e) {
          // Обработка нештатной ситуации
          //$error = $e->getMessage();
          ShippingLog::create([
              'code' => 'pochta',
              'title' => 'Ошибка при отправке заказа '.$order->id,
              'text' => $e->getMessage(),
          ]);

          $order->setStatus('is_processing');
          unset($orders[$key]);
          continue;
        }
      }
      if($orders->count() == $orders_count){
        $result = $this->client->createOrdersV2($pochta_orders);
      }else{
        return false;
      }

      Log::debug(print_r($result, true));
      if (isset($result['errors'])) {
        foreach($result['errors'] as $item_errors){
          foreach($item_errors['error-codes'] as $errors){
            ShippingLog::create([
                'code' => 'pochta',
                'title' => 'Ошибка при отправке заказа '.$pochta_orders[$item_errors['position']]['order-num'],
                'text' => $errors['description'].' ('.($errors['details']??'').')',
            ]);
          }
        }
      }
      if(isset($result['orders'])&&!empty($result['orders'])) {
        $r_ids = [];
        foreach($result['orders'] as $order_result){
          $r_ids[] = $order_result['order-num'];
          $order = $orders->where('id', $order_result['order-num'])->first();
//            if (!$order){
//              dd($order_result['order-num']);
//            }
          $data_shipping = $order->data_shipping;
          $data_shipping['pochta'] = $order_result;
          $order->update([
              'data_shipping' => $data_shipping
          ]);
          $order->setStatus('was_processed');
        }

        ShippingLog::create([
            'code' => 'pochta',
            'title' => denum(count($result['orders']), ['%d заказ','%d заказа','%d заказов']).' обработано',
            'text' => 'Заказы '.implode(', ', $r_ids),
        ]);
      }
      return $orders;
    }
}
