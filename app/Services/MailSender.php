<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\MailNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MailSender
{
  private $email;

  public function __construct($email){
    $this->email = $email;
  }

  public function confirmMeetingOrder(Order $order, User $user){
    $order_data = $order->data;
    if ($user->is_new) {
      $password = Str::random(8);
      $user->update([
          'password' => Hash::make($password),
          'is_new' => false
      ]);
      $pass_text = 'Ваш пароль: '.$password;
    }else{
      $pass_text = 'У вас уже есть аккаунт на сайте. Если не помните свой пароль, нажмите "восстановить пароль" на странице входа.';
    }
    $tg = Setting::where('key', 'tg_support')->first()->value;
    $mailmessage = (new MailMessage)
        ->subject('Ваш заказ '. $order->getOrderNumber() .' оплачен')
        ->greeting('Здравствуйте, ' . $order_data['form']['first_name'] . '!')
        ->line('Благодарим Вас за покупку!')
        ->line('Ваш номер заказа '. $order->getOrderNumber() .'.')
        ->line(new HtmlString('<br/>Личная встреча с Ольгой Нечаевой состоится 19 марта в 16:00.</br>'))
        ->line('📍По адресу - г.Москва, Пресненская Набережная 12, комплекс «Федерация», башня - «Восток»')
        ->line('Ресепшен «Воскток - 1», 29 этаж, офис 30А')
        ->line('Так же накануне вы получите дополнительную рассылку с описанием как добраться.')
        ->line('Телефон для связи +7 (904) 412-64-67 Екатерина.');
    $this->mailQueue($mailmessage);
  }
  public function confirmOrder(Order $order, User $user){
    $order_data = $order->data;
    $order_cart = $order->data_cart;
    $order_shipping = $order->data_shipping;

    $cart_text = '';
    $i = 1;
    foreach ($order_cart as $item) {
      if(isset($item['raffle'])&&$item['raffle']){
        continue;
      }
      $cart_text .= $i . '. ' . $item['name'] . ', ' . $item['qty'] . 'шт. на ' . formatPrice($item['qty'] * $item['price']) . '<br/>';
      $i++;
    }
    $cart_text = 'Ваша корзина:<br/>'.$cart_text;
    if($order_shipping['price']){
      $shipping_price = 'Стоимость доставки '.formatPrice($order_shipping['price']);
    }

    $discount = $order_data['discount'] ?? 0;
    $discount_text = '';
    if ($discount > 0 && $order_data['total'] + $order_shipping['price'] - $order->amount > 0) {
      if(isset($order_data['voucher'])){
        $discount_text = 'Прменен подарочный сертификат (' . $order_data['voucher']['code'] . '): -' . formatPrice($order_data['total'] + $order_shipping['price'] - $order->amount);
      }elseif(isset($order_data['promocode'])){
        $discount_text = 'Прменен промокод (' . $order_data['promocode']['code'] . '): -' . formatPrice($order_data['total'] + $order_shipping['price'] - $order->amount);
      }
    }
    $shipping_text = $this->getShippingText($order_shipping['shipping-code'], $order_shipping);

    if ($user->is_new) {
      $password = Str::random(8);
      $user->update([
          'password' => Hash::make($password),
          'is_new' => false
      ]);
      $pass_text = 'Ваш пароль: '.$password;
    }else{
      $pass_text = 'У вас уже есть аккаунт на сайте. Если не помните свой пароль, нажмите "восстановить пароль" на странице входа.';
    }
    $tg = Setting::where('key', 'tg_support')->first()->value;
    $mailmessage = (new MailMessage)
        ->subject('Ваш заказ '. $order->getOrderNumber() .' оплачен')
        ->greeting('Здравствуйте, ' . $order_data['form']['first_name'] . '!')
        ->line('Благодарим Вас за покупку!')
        ->line('Ваш номер заказа '. $order->getOrderNumber() .'.')
        ->line(new HtmlString('Информация о заказе доступна в личном кабинете <a href="' . route('cabinet.order.index') . '">' . route('cabinet.order.index') . '</a>'))
        ->line(new HtmlString('Ваш логин: ' . $user->email .'<br/>'.$pass_text))
        ->line(new HtmlString($cart_text));
    if(isset($shipping_price)&&$shipping_price){
      $mailmessage->line($shipping_price);
    }
    if($discount_text){
      $mailmessage->line($discount_text);
    }
//    if (Setting::where('key', 'happyCoupon')->first()->value && $order->giftCoupons()->exists()){
//      $hc_text = '<b>Поздравляем!</b><br/>';
//      $hc_text .= 'Тебе доступно участие в акции "Счастливый купон"!<br/>';
//      $hc_text .= 'Перейди по ссылке и проверь свою удачу: <a href="'.route('happy_coupon', $order->slug).'">Открыть купон</a><br/><br/>';
//      $mailmessage->line(new HtmlString($hc_text));
//    }
    $mailmessage->line('Итого '.formatPrice($order->amount))
        ->line(new HtmlString($shipping_text))
        ->line(new HtmlString('Если у Вас остались вопросы, обратитесь, пожалуйста, в техническую поддержку <a href="https://'.$tg.'">'.$tg.'</a>'))
        ->line(new HtmlString('С уважением, команда<br/>'.config('app.name')));
    $this->mailQueue($mailmessage);
  }
  public function confirmVouchersOrder(Order $order, User $user){
    $order_data = $order->data;
    $order_cart = $order->data_cart;

    if ($user->is_new) {
      $password = Str::random(8);
      $user->update([
          'password' => Hash::make($password),
          'is_new' => false
      ]);
      $pass_text = 'Ваш пароль: '.$password;
    }else{
      $pass_text = 'У вас уже есть аккаунт на сайте. Если не помните свой пароль, нажмите "восстановить пароль" на странице входа.';
    }
    $tg = Setting::where('key', 'tg_support')->first()->value;
    $mailmessage = (new MailMessage)
        ->subject('Ваш заказ '. $order->getOrderNumber() .' оплачен')
        ->greeting('Здравствуйте, ' . $order_data['form']['first_name'] . '!')
        ->line('Рады что в качестве подарка для своих близких и дорогих Вы выбираете продукцию LE MOUSSE ❤️')
        ->line('Ваш номер заказа '. $order->getOrderNumber() .'.')
        ->line(new HtmlString('Информация о заказе доступна в личном кабинете <a href="' . route('cabinet.order.index') . '">' . route('cabinet.order.index') . '</a>'))
        ->line(new HtmlString('Ваш логин: ' . $user->email .'<br/>'.$pass_text));
    $cart_text = '';
    $i = 1;
    foreach ($order_cart as $item) {
      if(!isset($item['vouchers'])){
        continue;
      }
      foreach($item['vouchers'] as $voucher){
        $cart_text .= $i . '. ' . $item['name'] . ' ' . $voucher[1] . ', ' . ' на ' . formatPrice($voucher[0]) . '<br/>';
        $mailmessage->attach(urlToStoragePath($voucher[2]));
        $i++;
      }
    }
    $cart_text = 'Ваши подарочные сертификаты:<br/>'.$cart_text;

    $cart_text .= 'Данный подарочный сертификат вы можете распечатать - рекомендуемый размер для печати 10*15 см<br/><br/>';

    $cart_text .= 'Каждый сертификат уникален - при использовании необходимо ввести код сертификата в специальном окошке в корзине.<br/>';
    $cart_text .= 'Воспользоваться сертификатом можно в течении 6 месяцев с момента покупки.<br/><br/>';
    $mailmessage->line(new HtmlString($cart_text));

    $mailmessage->line('Итого '.formatPrice($order->amount))
        ->line(new HtmlString('Если у Вас остались вопросы, обратитесь, пожалуйста, в техническую поддержку <a href="https://'.$tg.'">'.$tg.'</a>'))
        ->line(new HtmlString('С уважением, команда<br/>'.config('app.name')));
    $this->mailQueue($mailmessage);
  }
  public function trakingMessage(Order $order){
    $order_shipping = $order->data_shipping;
    if($order_shipping['shipping-code'] == 'boxberry'){
      $track = $order->data_shipping['boxberry']['track'] ?? null;
      $tracking_link = 'https://boxberry.ru/tracking-page?id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'yandex'){
      $track = $order->data_shipping['yandex']['track'] ?? null;
      $tracking_link = '#?id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'cdek'){
      $track = $order->data_shipping['cdek']['invoice_number'] ?? null;
      $tracking_link = 'https://www.cdek.ru/ru/tracking?order_id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'cdek_courier'){
      $track = $order->data_shipping['cdek_courier']['invoice_number'] ?? null;
      $tracking_link = 'https://www.cdek.ru/ru/tracking?order_id='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'pochta'){
      $track = $order->data_shipping['pochta']['barcode'] ?? null;
      $tracking_link = 'https://www.pochta.ru/tracking?barcode='.$track;
    }elseif($order->data_shipping['shipping-code'] == 'x5post'){
      $track = $order->data_shipping['x5post']['senderOrderId'] ?? null;
      $tracking_link = 'https://fivepost.ru/tracking/?id='.$track;
    }else{
      return false;
    }

    $tg = Setting::where('key', 'tg_support')->first()->value;
    $mailmessage = (new MailMessage)
        ->subject('Отслеживание заказа #'.$order->getOrderNumber().' '.config('app.name'))
        ->greeting('Уважаемый покупатель!')
        ->line('Ваш заказ '.config('app.name').' #'.$order->getOrderNumber().' создан.')
        ->line('Отследить посылку вы можете по треку '.$track.' на официальном сайте транспортной компании')
        ->line(new HtmlString('Отслеживание доступно по ссылке: <a href="'.$tracking_link.'" target="_blank">Отследить заказ</a>'))
        ->line('Получите ,пожалуйста, посылку  в течении 1-2 дней. В противном случае, при долгом хранении товара в пунктах выдачи, он может испортиться при неподходящих климатических условиях.')
        ->line(new HtmlString('Срок хранения посылки в пвз <strong>14 дней</strong>.<br/>Срок хранения посылки в постамате <strong>3 дня.</strong>'))
        ->line(new HtmlString('Если у вас остались вопросы обратитесь пожалуйста к нам <a href="https://'.$tg.'">'.$tg.'</a>'));
    $this->mailQueue($mailmessage);
  }

  public function productNotification(Product $product){
    $mailmessage = (new MailMessage)
        ->subject('Товар, который вы ждали снова в наличии! '.config('app.name'))
        ->greeting('Уважаемый покупатель!')
        ->line($product->name.' снова в наличии')
        ->line(new HtmlString('Оформить заказ можно по ссылке: <a href="'.route('product.index', $product->slug).'" target="_blank">Открыть на сайте</a>'));
    $this->mailQueue($mailmessage);
  }
  public function birthdayGreetings($bonuses){
    $mailmessage = (new MailMessage)
        ->subject('Вам начислены бонусы '.config('app.name'))
        ->greeting('Добрый день!')
        ->line('Накануне Вашего дня рождения мы начислили в качестве комплимента '.$bonuses.' подарочных бонусов!')
        ->line('С любовью - '.config('app.name'));
    $this->mailQueue($mailmessage);
  }

  public function customMessage($subject, $text){
    $mailmessage = (new MailMessage)
        ->subject($subject)
        ->line($text);
    $this->mailQueue($mailmessage);
  }
  public function sendApiToken($token){
    $mailmessage = (new MailMessage)
        ->subject('Создан API-токен')
        ->line('Добрый день,')
        ->line('Ваш токен для доступа к API '.config('app.name').': <i><b>'.$token.'</b></i>')
        ->line('Храните его в безопасности.')
        ->line('Если будут вопросы — пишите в нашу поддержку.')
        ->line('С уважением, команда<br/>'.config('app.name'));
    $this->mailQueue($mailmessage);
  }
  public function remindAboutReview(Order $order){
    $tg = Setting::where('key', 'tg_support')->first()->value;
    $mailmessage = (new MailMessage)
        ->subject('Скидка на следующий заказ на '.config('app.name'))
        ->line('Мы хотим предоставить Вам вашу ЛИЧНУЮ скидку 5% на следующий заказ, за Ваш честный отзыв на нашем сайте - <a href="'.route('cabinet.order.show', $order->slug).'" target="_blank">Оставить отзыв</a>')
        ->line('Если будут вопросы — пишите в нашу поддержку <a href="https://'.$tg.'">'.$tg.'</a>.')
        ->line('С уважением, команда<br/>'.config('app.name'));
    $this->mailQueue($mailmessage);
  }
  public function promo1(){
    $mailmessage = (new MailMessage)
        ->subject('Золотой билет')
        ->line('<img src="'.asset('telegram/tg2024-07-14.jpg').'" alt="Золотой билет" style="width: 100%">')
        ->line('<b>ПОЛУЧИ СВОЙ «ЗОЛОТОЙ БИЛЕТ» С КРУТЫМИ ПОДАРКАМИ!🤩</b>')
        ->line('🔥Покупая mini-сеты (они еще и со СКИДКОЙ),<br/>у тебя есть шанс выиграть поездку в Турцию ✈️ и другие ценные подарки!🔥')
        ->line('<a href="https://lemousse.shop/catalog/seti_mini_versiy">Смотреть предложение</a>.')
        ->line('🚛Во время акции<br/>ДОСТАВКА БЕСПЛАТНАЯ')
        ->line('P.S. у тебя есть ровно сутки, но сеты могут закончиться быстрее!😉')
        ->line('С уважением, команда<br/>'.config('app.name'));
    $this->mailQueue($mailmessage);
  }
  public function promo2($voucher){
    $mailmessage = (new MailMessage)
        ->subject('Срок действия сертификата истекает')
//        ->line('<img src="'.asset('telegram/tg2024-07-14.jpg').'" alt="Золотой билет" style="width: 100%">')
        ->line('<b>Срок действия твоего подарочного сертификата скоро закончится!</b>')
        ->line('<b>Успей воспользоваться!</b>')
        ->line($voucher)
        ->line('Кстати, сейчас на сайте выгодные цены на сеты с mini-версиями, например:')
        ->line('Увлажняющий крем (мини) + Скраб для лица (мини) - со скидкой 3.150 ₽ а с твоим сертификатом - <b>всего 2.150 ₽ + бесплатная доставка!</b>')
        ->line('Предложение действует до 21:00 15 июля.')
        ->line('<a href="https://lemousse.shop/catalog/seti_mini_versiy">Посмотреть все предложения</a>')
        ->line('С уважением, команда<br/>'.config('app.name'));
    $this->mailQueue($mailmessage);
  }
  public function orderNotification(Order $order){
    $mailmessage = (new MailMessage)
        ->subject('Заказ '.$order->getOrderNumber().' ожидает оплату')
        ->line('Добрый день, '.$order->data['form']['first_name'].'!')
        ->line('Вы создали заказ '.$order->getOrderNumber())
        ->line('Сумма заказа: '.formatPrice($order->amount))
        ->line('Ваш заказ не оплачен, вы еще  можете оплатить его по ссылке:')
        ->line('<a href="'.route('order.robokassa', $order->slug).'">Перейти к оплате</a>')
        ->line('С уважением, команда<br/>'.config('app.name'));
    $this->mailQueue($mailmessage);
  }
  private function mailQueue(MailMessage $mailMessage){
    Notification::route('mail', $this->email)->notify(new MailNotification($mailMessage));
  }

  private function getShippingText($code, $order_shipping){
    if(in_array($code, ['pochta', 'cdek', 'cdek_courier', 'yandex','x5post'])){ // Доставка ТК
      $shipping_text = 'Отправка заказа, согласно вашего выбора, будет осуществлена ';
      if($code == 'pochta'){
        $shipping_text .= 'Почтой России';
      }elseif($code == 'cdek'){
        $shipping_text .= 'СДЭК до пункта выдачи заказов';
      }elseif($code == 'cdek_courier'){
        $shipping_text .= 'СДЭК курьером до двери';
      }elseif($code == 'boxberry'){
        $shipping_text .= 'Boxberry до пункта выдачи заказов';
      }elseif($code == 'yandex'){
        $shipping_text .= 'Яндекс Доставка до пункта выдачи заказов';
      }elseif($code == 'x5post'){
        $shipping_text .= '5 Пост до пункта выдачи заказов';
      }
      $shipping_text .= '<br/><br/>';
      $shipping_text .= 'Внимание!<br/>Обработка заказа занимает до 10 рабочих дней, не включая дня его оформления. <br/><br/>';
      if($code == 'pochta'){
        $shipping_text .= 'Адрес доставки: ' . $order_shipping['full_address'];
      }elseif($code == 'cdek'){
        $shipping_text .= 'Пункт выдачи заказов: ' . $order_shipping['cdek-pvz-address'];
      }elseif($code == 'cdek_courier'){
        $shipping_text .= 'Адрес доставки: ' . $order_shipping['cdek_courier-form-address'];
      }elseif($code == 'boxberry'){
        $shipping_text .= 'Пункт выдачи заказов: '. $order_shipping['boxberry-pvz-address'];
      }elseif($code == 'yandex'){
        $shipping_text .= 'Пункт выдачи заказов: '. $order_shipping['yandex-pvz-address'];
      }elseif($code == 'x5post'){
        $shipping_text .= 'Пункт выдачи заказов: '. $order_shipping['x5post-pvz-address'];
      }
      $shipping_text .= '<br/><br/>';
      $shipping_text .= 'После отправки продукции на почтовый ящик вам будет отправлен трек-номер для отслеживания.<br/><br/>';
    }elseif($code == 'pickup'){ // Самовывоз в Волгограде
      $shipping_text = 'Вы выбрали самовывоз.<br/>Забрать заказ можно после того, как статус будет “готов к выдаче”. <br/>Отслеживать статус заказа необходимо в личном кабинете. Как только заказ будет готов к выдаче, Вы получите sms. <br/><br/>';
      $shipping_text .= 'Внимание!<br/>Обработка заказа занимает до 10 рабочих дней, не включая дня его оформления.<br/><br/>';
      $shipping_text .= 'Обращаем Ваше внимание на то, что заказ необходимо получить в течение 10 (десяти) рабочих дней после смены статуса “Готов к выдаче”.<br/><br/>';
      $shipping_text .= 'Режим работы пункта выдачи:<br/>пн-пт с 11:00 до 20:00, сб-вс с 09:00 до 18:00<br/>Адрес пункта выдачи <br/>г. Волгоград, пр-т Жукова д. 100б (вход через магазин “Магнит” справа напротив “Золушки”)<br/>';
      if(!Setting::where('key', 'happyCoupon')->first()->value&&!Setting::where('key', 'promo_1+1=3')->first()->value){
        $shipping_text .= '<br/><a href="https://wa.me/message/J6HM6AOKFBDGI1">Заказать курьерскую доставку</a><br/>';
      }
    }else{ // остальные пукнты самовывоза
      $pickup = Pickup::select()->where('code', $code)->first();
      if($pickup){
        $shipping_text = 'Вы выбрали самовывоз.<br/><br/>';
        $shipping_text .= 'Ваш заказ будет обработан в ближайшие сутки.<br/>';
        $shipping_text .= 'После изменения статуса заказа в личном кабинете на статус «ГОТОВ К ВЫДАЧЕ» его можно забрать по адресу:<br/>';
        $shipping_text .= $pickup->address.'.<br/><br/>';

        $shipping_text .= 'Для получения заказа необходимо будет назвать своё ФИО и номер заказа.<br/><br/>';

        $shipping_text .= 'Время работы самовывоза:<br/>';
        $shipping_text .= ($pickup->params['times'] ?? '').', <br/>';
        $shipping_text .= 'Телефон для консультации <br/>';
        $shipping_text .= '☎  '.$pickup->phone.'<br/><br/>';
      }
    }
    return $shipping_text;
  }
}
