<?php

namespace App\Services;

use App\Models\Country;
use App\Models\Order;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\TgChat;
use App\Models\User;
use App\Notifications\MailNotification;
use App\Notifications\TelegramNotification;
use Exception;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class TelegramSender
{
  private $tgChat;

  public function __construct(TgChat $tgChat){
    $this->tgChat = $tgChat;
  }

  public function confirmMeetingOrder(Order $order, User $user){
    $text = "Благодарим за покупку!\n\nВаш заказ ".$order->getOrderNumber()." оплачен ✔️\n\n";
    $text .= "Личная встреча с Ольгой Нечаевой состоится 19 марта в 16:00.\n\n";
    $text .= "📍По адресу - г.Москва, Пресненская Набережная 12, комплекс «Федерация», башня - «Восток»\n";
    $text .= "Ресепшен «Воскток - 1», 29 этаж, офис 30А\n\n";
    $text .= "Так же накануне вы получите дополнительную рассылку с описанием как добраться.\n\n";
    $text .= "Телефон для связи +7 (904) 412-64-67 Екатерина.\n\n";
    $text .= "_\nLe Mousse – с заботой о твоей коже.";
    $this->telegramQueue($text);
  }
  public function confirmOrder(Order $order, User $user){
    $text = "Благодарим за покупку!\n\nВаш заказ ".$order->getOrderNumber()." оплачен ✔️\n\n";
    $text .= "Информация о заказе доступна в личном кабинете ".route('cabinet.order.index')."\n\n";
    $text .= "_\nLe Mousse – с заботой о твоей коже.";
    $this->telegramQueue($text);
  }

  public function orderNotification(Order $order){

    $text = "Добрый день, ".$order->data['form']['first_name']."!\n\n";
    $text .= "Вы создали заказ ".$order->getOrderNumber()."\n";
    $text .= "Сумма заказа: ".formatPrice($order->amount)."\n\n";
    $text .= "Ваш заказ не оплачен, вы еще  можете оплатит его по ссылке:\n\n";
    $text .= route('order.robokassa', $order->slug)."\n\n";
    $text .= "_\nLe Mousse – с заботой о твоей коже.";
    $this->telegramQueue($text);
  }
  public function confirmVouchersOrder(Order $order, User $user){
    $order_cart = $order->data_cart;
    $files = [];
    foreach ($order_cart as $item) {
      if(!isset($item['vouchers'])){
        continue;
      }
      foreach($item['vouchers'] as $voucher){
        $files[] = urlToStoragePath($voucher[2]);
      }
    }

    $text = "Благодарим за покупку!\n\nВаш заказ ".$order->getOrderNumber()." оплачен ✔️\n\n";
    $text .= "Информация о заказе доступна в личном кабинете ".route('cabinet.order.index')."\n\n";
    $text .= "🔗 Данный сертификат вы можете распечатать - рекомендуемый размер для печати 10*15 см\n\n";
    $text .= "Каждый сертификат уникален - при использовании необходимо ввести код сертификата в специальном окошке в корзине.\n\n\n";
    $text .= "_\nLe Mousse – с заботой о твоей коже.";
    $this->telegramQueue($text);
    $this->telegramQueue($files, 'files');
  }
  public function trakingMessage(Order $order){
    $order_shipping = $order->data_shipping;
    if($order_shipping['shipping-code'] == 'boxberry'){
      $track = $order->data_shipping['boxberry']['track'] ?? null;
      $tracking_link = 'https://boxberry.ru/tracking-page?id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'cdek'){
      $track = $order->data_shipping['cdek']['invoice_number'] ?? null;
      $tracking_link = 'https://www.cdek.ru/ru/tracking?order_id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'cdek_courier'){
      $track = $order->data_shipping['cdek_courier']['invoice_number'] ?? null;
      $tracking_link = 'https://www.cdek.ru/ru/tracking?order_id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'pochta'){
      $track = $order->data_shipping['pochta']['barcode'] ?? null;
      $tracking_link = 'https://www.pochta.ru/tracking?barcode='.$track;
    }else{
      return false;
    }
    $text = "Отслеживание заказа\n";
    $text .= "Ваш заказ #".$order->getOrderNumber()." создан\n\n";
    $text .= "Отследить посылку вы можете по треку $track на официальном сайте транспортной компании\n\n";
    $text .= "Отслеживание доступно по ссылке $tracking_link\n\n";
    $text .= "Получите, пожалуйста, посылку  в течении 1-2 дней. В противном случае, при долгом хранении товара в пунктах выдачи, он может испортиться при неподходящих климатических условиях.";
    $text .= "Срок хранения посылки в пвз 14 дней.\nСрок хранения посылки в постамате 3 дня.";
    $text .= "_\nLe Mousse – с заботой о твоей коже.";
    $messageBefore = $this->tgChat->tgMessages()
        ->where('text', 'like', '%Ваш заказ #'.$order->getOrderNumber().' создан%')
        ->where('created_at', '>', now()->startOfDay()->format('Y-m-d H:i:s'))
        ->exists();
    if(!$messageBefore){
      $this->telegramQueue($text);
    }else{
      return false;
    }
  }

  public function productNotification(Product $product){
    $text = "$product->name снова в наличии🔥\n\nОформить заказ можно по ссылке: ".route('product.index', $product->slug);
    $text .= "\n\n_\nLe Mousse – с заботой о твоей коже.";
    $this->telegramQueue($text);
  }

  public function birthdayGreetings($bonuses){
    $text = "Добрый день!🎁\n\nНакануне Вашего дня рождения мы начислили в качестве комплимента $bonuses подарочных бонусов!";
    $text .= "\n\n_\nLe Mousse – с заботой о твоей коже.";
    $this->telegramQueue($text);
  }

  public function customMessage($text){
    $this->telegramQueue($text);
  }

  private function telegramQueue($text, $type = 'text_message'){
    $this->tgChat->notify(new TelegramNotification($text, $type));
  }
}
