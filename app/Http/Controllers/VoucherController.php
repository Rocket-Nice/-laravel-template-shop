<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Country;
use App\Models\Coupone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Phone;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Services\MailSender;
use App\Services\TelegramSender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    public function catalog(){
      $products = Product::select('id', 'name', 'sku', 'volume', 'style_cards', 'style_page->cardImage as cardImage', 'style_page->cardsDescription as cardsDescription', 'style_page->cardsDescriptionIcons as cardsDescriptionIcons', 'product_options', 'slug', 'status', 'quantity', 'data_status', 'data_quantity', 'price', 'old_price', 'hidden', 'preorder')
          ->where('type_id', 6)->where('hidden', false);
      $products = $products->get();
      $content = Content::where('route', Route::currentRouteName())->first();
      if (!$content||!$content->active){
        abort(404);
      }
      $seo = [
          'title' => 'Подарочные сертификаты'
      ];
      return view('template.public.product.vouchers', compact('products', 'content', 'seo'));
    }

    public function order($voucher_sku){
      $voucher = Product::select('id', 'name', 'sku', 'volume', 'style_page->cardImage->image->200 as image', 'slug', 'status', 'quantity', 'data_status', 'data_quantity', 'price', 'old_price', 'hidden', 'preorder')
            ->where('sku', $voucher_sku)->where('type_id', 6)->where('hidden', false)->first();
      if(!$voucher){
        abort(404);
      }
      if (!$voucher->getStock()) {
        return back()->withInput()->withErrors([
            'message' => 'Данного сертификата нет в наличии'
        ]);
      }
      $content = Content::where('id', 11)->first();
      $content->text_data = ['headline1' => 'Оформление заказа'];
      $seo = [
          'title' => 'Купить подарочный сертификат'
      ];
      return view('template.public.order.vouchers', compact('seo', 'voucher', 'content'));
    }

  public function submit(Request $request){
    $this->validateOrder($request);
    $phone = preg_replace("/[^,.0-9]/", '', $request->phone);
    if (preg_match('/^(89|79|9)\d{7,9}$/', $phone)) {
      // Применяем preg_replace, чтобы заменить начало номера
      $phone = preg_replace('/^(89|79|9)/', '+79', $phone);

      // Дополнительная проверка и замена, если номер начинается с '9'
      if ($phone[0] == '9') {
        $phone = '+7' . $phone;
      }

      // Дальнейшие действия с номером $phone
      // ...
    }
    $email = str_replace(' ', '', strtolower(trim($request->email, ' ')));
    $name = $request->last_name . ' ' . $request->first_name;
    if (isset($request->middle_name) && !empty($request->middle_name)) {
      $name .= ' ' . $request->middle_name;
    }
    if (auth()->check()){
      $user = auth()->user();
    }else{
      $user = User::select('id', 'phone', 'options')->where(DB::raw('lower(email)'), '=', $email)->first();
    }
    if (!$user) {
      $user = User::where('phone', '=', $phone)->first();
      if ($user) {
        return back()->withInput()->withErrors([
            'message' => 'Данный телефон уже используется другим пользователем'
        ]);
      }
      $password = Str::random(8);

      $user = User::create([
          'uuid' => (string) Str::uuid(),
          'name' => $name,
          'last_name' => $request->last_name,
          'first_name' => $request->first_name,
          'middle_name' => $request->middle_name,
          'phone' => $phone,
          'email' => $email,
          'password' => Hash::make($password),
          'is_new' => true
      ]);

      $credentials = array('email' => $email, 'password' => $password);
      if (\Illuminate\Support\Facades\Auth::attempt($credentials, true)) {
        $request->session()->regenerate();
      }
    } else {
      $user_params = [];
      if ($user->phone != $phone) {
        $user_phone = User::where('phone', '=', $phone)->where('id', '!=', $user->id)->exists();
        if ($user_phone) {
          return back()->withInput()->withErrors([
              'message' => 'Данный телефон уже используется другим пользователем'
          ]);
        } else {
          $user_params['phone'] = $phone;
        }
      }else{
        if(!$user->last_name||!$user->first_name||!$user->middle_name){
          if(!$user->last_name&&$request->last_name){
            $user_params['last_name'] = $request->last_name;
          }
          if(!$user->first_name&&$request->first_name){
            $user_params['first_name'] = $request->first_name;
          }
          if(!$user->middle_name&&$request->middle_name){
            $user_params['middle_name'] = $request->middle_name;
          }
        }
      }
      if(!empty($user_params)){
        $user->update($user_params);
      }
    }
    if (isset($user->options['blocked'])&&$user->options['blocked']){
      return back()->withInput()->withErrors([
          'message' => 'Что-то пошло не так.'
      ]);
    }
    // добавлем телефон в базу
    $phone_db = Phone::where('number', $phone)->first();
    if (!$phone_db) {
      $phone_db = Phone::create([
          'number' => $phone,
          'user_id' => $user->id
      ]);
    }
    if ($phone_db->user_id&&$phone_db->user_id!=$user->id&&$phone_db->confirmed){
      return back()->withInput()->withErrors([
          'message' => 'Данный телефон '.$phone.' уже используется другим пользователем.'
      ]);
    }elseif($phone_db->user&&$phone_db->user_id!=$user->id){
      $phone_db->update([
          'user_id' => $user->id
      ]);
    }
    $user->update([
        'is_subscribed_to_marketing' => $request->mailing ? true : false
    ]);
    // готовим данные для заказа
    $data = [
        'total' => 0,
        'is_voucher' => true,
        'form' => [
            'email' => $email,
            'instagram' => $request->instagram,
            'phone' => $phone,
            'full_name' => $name,
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'oferta' => $request->oferta,
            'politika' => $request->politika,
            'mailing' => $request->mailing,
        ]
    ];
    // проверяем корзину
    $products = Product::select('id', 'name', 'sku', 'volume', 'style_page->cardImage->image->200 as image', 'slug', 'status', 'quantity', 'data_status', 'data_quantity', 'price', 'old_price', 'hidden', 'preorder')
        ->where('type_id', 6)->where('hidden', false)->whereIn('id', $request->products);
    $products = $products->get();
    foreach($products as $product){
      if(!$product->getStock()){
        return back()->withInput()->withErrors([
            'message' => 'Данный сертификат недоступен для покупки'
        ]);
      }
    }

    $data_cart = [];
    $total = 0;
    foreach ($products as $item) {
      $new_item = [
          'id' => $item->id,
          'name' => $item->name,
          'price' => $item->price,
          'qty' => 1,
          'model' => $item->sku,
          'image' => $item->image,
      ];

      $data_cart[] = $new_item;
      $total += $item->price;
    }

    if(empty($data_cart)){
      return back()->withErrors(['Ошибка']);
    }
    $data['total'] = $total;
    $data_shipping = [
        'shipping-code' => null,
        'shipping-method' => null,
        'country_code' => null,
        'price' => 0
    ];

    $data['all_fields'] = $request->toArray();
    foreach($data['all_fields'] as $key => $value){
      if($value === null){
        unset($data['all_fields'][$key]);
      }
    }
    $slug = getCode(4);
    $retult_total = $total;

    $order = Order::create([
        'user_id' => $user->id,
        'data' => $data,
        'data_cart' => $data_cart,
        'data_shipping' => $data_shipping,
        'amount' => $retult_total,
        'confirm' => 0,
        'slug' => $slug,
        'partner_id' => $partner ?? null
    ]);
    $order->update([
        'slug' => $order->getOrderNumber().'_'.$order->slug
    ]);

    // обновить корзину
    $data_cart = $order->data_cart;
    $cart_ids = [];
    $cart_qty = [];
    foreach($data_cart as $cart_item){
      $orderItem = OrderItem::setParams($order->id, $cart_item);
      $cart_ids[] = $cart_item['id'];
      $cart_qty[$cart_item['id']] = $cart_item['qty'];
      if(isset($cart_item['parent_product'])){
        $cart_ids[] = $cart_item['parent_product'];
        $cart_qty[$cart_item['parent_product']] = $cart_item['qty'];
      }
    }
    $products = Product::whereIn('id', $cart_ids)->get();
    foreach ($products as $product) {
      DB::update('UPDATE `products` SET `quantity`=`quantity`-'.$cart_qty[$product->id].' WHERE `id` = ' . $product->id . ';');
    }
    Product::flushQueryCache();
    return redirect()->route('order.robokassa', $order->slug);
  }

  private function validateOrder(Request $request){
    $validate = [];
    $validate['instagram'] = ['nullable', 'string', 'max:255'];
    $validate['first_name'] = ['required', 'string', 'max:30'];
    $validate['middle_name'] = ['nullable', 'string', 'max:30'];
    $validate['last_name'] = ['required', 'string', 'max:30'];
    $validate['email'] = ['required', 'string', 'email:rfc,dns', 'max:255', 'confirmed'];
    $validate['phone'] = ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'];

    $validate['oferta'] = ['required', 'string', 'max:30'];
    $validate['politika'] = ['required', 'string', 'max:30'];


    $validate_exceptions = [
        'instagram.max' => 'Слишком много символов в инстаграм',
        'first_name.max' => 'Слишком много букв в имени',
        'middle_name.max' => 'Слишком много букв в отчестве',
        'last_name.max' => 'Слишком много букв в фамилии',
        'email.required' => 'Укажите ваш email',
        'email.email' => 'Кажется вы ошиблись в email адресе',
        'email.confirmed' => 'Email адреса не совпадают',
        'phone.required' => 'Укажите ваш телефон',
        'phone.regex' => 'Кажется, вы ошиблись в телефоне',
        'oferta.required' => 'Примите условия оферты',
        'politika.required' => 'Примите политику обработки персональных данных',
        'gift-politika.required' => 'Примите условия акции',
        'gift-delivery.required' => 'Примите сроки доставки заказа',
        'country.required' => 'Выберите страну доставки',
        'shipping-price.required' => 'Стоимость доставки не рассчитана',
        'shipping-price.min' => 'Стоимость доставки не рассчитана',
        'shipping-price.numeric' => 'Стоимость доставки не рассчитана',
        'shipping-code.required' => 'Не выбран способ доставки',
        'shipping-code.in' => 'Выбран недоступный способ доставки',
        'cdek-pvz-id.required' => 'Не выбран пункт выдачи заказов СДЭК',
        'cdek-pvz-address.required' => 'Не выбран пункт выдачи заказов СДЭК',
        'boxberry-pvz-id.required' => 'Не выбран пункт выдачи заказов Boxberry',
        'boxberry-pvz-address.required' => 'Не выбран пункт выдачи заказов Boxberry',
        'shipping-agry.required' => 'Примите условия международной доставки почтой',
        'postcode.required' => 'Укажите почтовый индекс',
        'region.required' => 'Укажите регион или область',
        'city.required' => 'Укажите город',
        'street.required' => 'Укажите улицу',
        'house.required' => 'Укажите дом',
    ];
    $request->validate($validate,$validate_exceptions);
    return true;
  }

  public function createVouchers(Order $order){
    $user = $order->user;
    $cart_data = $order->data_cart;
    $vouchers = [];
    $cart_updated = false;
    foreach($cart_data as $key => $item){
      if(isset($item['vouchers'])){
        continue;
      }
      $product = Product::where('id', $item['id'])->where('type_id', 6)->first();
      if(!$product){
        continue;
      }
      $new_vouchers = (new \App\Http\Controllers\Admin\Promo\VoucherController)->create_voucher($item['price'], $item['qty']);

      if(is_array($new_vouchers)){
        $cart_data[$key]['vouchers'] = $new_vouchers;
        $vouchers = array_merge($vouchers, $new_vouchers);
        $cart_updated = true;
      }
    }
    if($cart_updated){
      $order->update([
          'data_cart' => $cart_data
      ]);
    }

    (new MailSender($user->email))->confirmVouchersOrder($order, $user);
    foreach($user->tgChats as $tgChat){
      (new TelegramSender($tgChat))->confirmVouchersOrder($order, $user);
    }
    $order->setStatus('was_delivered');
    return true;
  }
}
