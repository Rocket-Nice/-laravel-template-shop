<?php

namespace App\Http\Controllers;

use App\Models\BoxberryPvz;
use App\Models\CdekCity;
use App\Models\CdekPvz;
use App\Models\Content;
use App\Models\Country;
use App\Models\Coupone;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Phone;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherTransactions;
use App\Models\CatInBagPreview;
use App\Services\HappyCoupon;
use App\Services\CatInBag\GameService;
use App\Services\CatInBag\PreviewService;
use App\Services\MailSender;
use App\Services\Promo113;
use App\Services\TelegramSender;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private $cart;

    public function __construct()
    {
        $this->cart = Cart::instance('cart');
    }

    public function index()
    {
        $cart = $this->cart;
        $countries = Country::select('id', 'name', 'options')->orderAvailable()->get();

        $shipping_methods = ShippingMethod::select('code', 'name')->where('active', true)->where('code', '!=', 'pochta')->get();
        $pickups = Pickup::select('code', 'name', 'address', 'params')->order()->get();

        $cart_info = [
            'only_pickups' => [] // информация о товарах, которые недоступны для доставки
        ];
        $delivery_status = true; // true - возможно доставка, иначе только самовывоз
        $old_total = 0; // цена до скидки
        $alert = ''; // сообщения с предупреждениями
        // проверяем наличие
        $productIds = $cart->content()->map(function ($item) {
            return data_get($item, 'options.product_id');
        })->all();
        $cartProducts = Product::dontCache()->select(
            'id',
            'sku',
            'name',
            'quantity',
            'status',
            'price',
            'options',
            'data_status',
            'data_quantity',
            'product_options',
            'options->coupones as coupones'
        )->whereIn('id', $productIds)->get();

        foreach ($cart->content() as $item) {
            if ($item->options->gift) {
                continue;
            }
            $product = $cartProducts->where('id', '=', $item->options->product_id)->first();
            if ($product->optionProducts()->exists()) {
                $cart->remove($item->rowId);
                if (getSettings('promo_1+1=3')) {
                    (new CartController)->promotion();
                }
                if (getSettings('puzzlesStatus')) {
                    (new CartController)->puzzles();
                }
                $alert .= 'Для товара «' . $product->name . '» необходимо выбрать опции<br/>';
            }
            $is_in_stock = false; // товар в наличие хотя бы для одного способа доставки
            // проверяем наличе для самовывоза
            //      foreach($pickups as $key => $pickup){
            //        $product_status = $product->data_status[$pickup->params['status']] ?? 0;
            //        $product_quantity = $product->data_quantity[$pickup->params['quantity']] ?? 0;
            //        if ($product_quantity < $item->qty || $product_status != 1) {
            //          unset($pickups[$key]);
            //        }else{
            //          $is_in_stock = true;
            //        }
            //      }
            // проверяем наличие для доставки
            if ($product->quantity < $item->qty || !$product->status) {
                $delivery_status = false;
                $cart_info['only_pickups'][] = $item->rowId;
            } else {
                $is_in_stock = true;
            }
            // удаляем товар из корзины, если его нет совсем
            if (!getSettings('promo_1+1=3') && substr($item->id, -strlen('_discounted')) == '_discounted') {
                $cart->remove($item->rowId);
            }
            if (!$is_in_stock) {
                $cart->remove($item->rowId);
                if (getSettings('promo_1+1=3')) {
                    (new CartController)->promotion();
                }
                if (getSettings('puzzlesStatus')) {
                    (new CartController)->puzzles();
                }
                $alert .= 'Товар «' . $product->name . '» отсутствует в достаточном количестве на складе<br/>';
            }
            // старая цена
            if ($item->options->old_price) {
                $old_total += $item->options->old_price * $item->qty;
            } else {
                $old_total += $item->price * $item->qty;
            }
        }
        // сумма товаров в корзине, к которым можно применить промокод
        $total_for_discount = $cart->content()->filter(function ($cartItem) {
            return !$cartItem->options->has('discount_does_not_apply') || $cartItem->options->discount_does_not_apply == false;
        })->sum(function ($cartItem) {
            return $cartItem->price * $cartItem->qty;
        });
        $user = auth()->user();

        $need_to_keys = '';

        if (getSettings('promo20')) {
            if ($cart->subtotal(0, '.', '') < 3599) {
                $need_to_keys = 'Пополните корзину на <span class="font-bold cormorantInfant">' . formatPrice(3599 - $cart->subtotal(0, '.', '')) . '</span>, чтобы участвовать в акции';
            }
        } else { // if(getSettings('promo30'))
            //      $count = $cart->content()->sum(function ($item) {
            //        return !$item->options->gift ? $item->qty : 0;
            //      });
            //      if ($count < 3) {
            //        $need_to_keys = 'Добавьте ещё '.denum(3 - $count, ['<span style="font-style:italic;line-height:0;font-size:2.5em;">%d</span> позицию', '<span style="font-style:italic;line-height:0;font-size:2.5em;">%d</span> позиции', '<span style="font-style:italic;line-height:0;font-size:2.5em;">%d</span> позиций']).' в корзину и получите бесплатную доставку!';
            //      }else{
            //        $need_to_keys = 'Ваша доставка будет бесплатной!*';
            //      }
        }

        $seo = [
            'title' => 'Оформление заказа'
        ];

        $content = Content::where('route', Route::currentRouteName())->first();

        return view('template.public.order.index', compact(
            'seo',
            'cart',
            'total_for_discount',
            'user',
            'countries',
            'shipping_methods',
            'pickups',
            'delivery_status',
            'cart_info',
            'old_total',
            'alert',
            'content',
            'need_to_keys',
        ));
    }

    public function submit(Request $request)
    {
        $cart = $this->cart;
        $this->validateOrder($request);

        $phone = preg_replace("/[^,.0-9]/", '', $request->phone);
        if (isset($request->country) && $request->country == 0) {
            $phone = preg_replace('/^(89|79|9)/', '+79', $request->phone);
            if ($phone[0] == '9') {
                $phone = '+7' . $phone;
            }
        }
        $email = str_replace(' ', '', strtolower(trim($request->email, ' ')));
        $name = $request->last_name . ' ' . $request->first_name;
        if (isset($request->middle_name) && !empty($request->middle_name)) {
            $name .= ' ' . $request->middle_name;
        }
        if (auth()->check()) {
            $user = auth()->user();
        } else {
            $user = User::select('id', 'email', 'phone', 'options')->where(DB::raw('lower(email)'), '=', $email)->first();
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
            } else {
                if (!$user->last_name || !$user->first_name || !$user->middle_name) {
                    if (!$user->last_name && $request->last_name) {
                        $user_params['last_name'] = $request->last_name;
                    }
                    if (!$user->first_name && $request->first_name) {
                        $user_params['first_name'] = $request->first_name;
                    }
                    if (!$user->middle_name && $request->middle_name) {
                        $user_params['middle_name'] = $request->middle_name;
                    }
                }
            }
            if (!empty($user_params)) {
                $user->update($user_params);
            }
        }
        if (isset($user->options['blocked']) && $user->options['blocked']) {
            return back()->withInput()->withErrors([
                'message' => 'Что-то пошло не так.'
            ]);
        }
        if (!$user->pages()->where('id', 1)->exists()) {
            $page = Page::find(1);
            $page->users()->attach($user->id);
        }
        // добавлем телефон в базу
        $phone_db = Phone::where('number', $phone)->first();
        if (!$phone_db) {
            $phone_db = Phone::create([
                'number' => $phone,
                'user_id' => $user->id
            ]);
        }
        if ($phone_db->user_id && $phone_db->user_id != $user->id && $phone_db->confirmed) {
            return back()->withInput()->withErrors([
                'message' => 'Данный телефон ' . $phone . ' уже используется другим пользователем.'
            ]);
        } elseif ($phone_db->user && $phone_db->user_id != $user->id) {
            $phone_db->update([
                'user_id' => $user->id
            ]);
        }
        // подписка на рассылки
        $user->update([
            'is_subscribed_to_marketing' => $request->mailing ? true : false
        ]);
        // готовим данные для заказа
        $data = [
            'total' => 0,
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
        if (getSettings('catInBag')) {
            $catInBagParticipated = true;
            $data['cat_in_bag_participated'] = $catInBagParticipated;
            if ($catInBagParticipated) {
                $previewData = app(PreviewService::class)->getPreview($request);
                if (!empty($previewData['preview']?->category_ids)) {
                    $data['cat_in_bag_visible_category_ids'] = $previewData['preview']->category_ids;
                }
            }
        }
        // проверяем промокоды и сертификаты
        $discount = 0;
        $referrer = null;
        $partner = null;
        $userBonuses = $user->getBonuses();
        // золотой час diamondPromo2
        if (getSettings('diamondPromo2')) {
            $userBonuses += $user->getSuperBonuses();
        }
        if (($request->promocode || $request->voucher || $request->bonuses) && $request->discount > 0) {
            if ($request->promocode && !$request->voucher && !$request->bonuses) {
                $promocode = $this->checkPromocode($request);
                if (isset($promocode['promocode_discount']) && $promocode['promocode_discount'] > 0) {
                    $code = mb_strtolower($request->promocode);
                    $promo = Coupone::query()->where(DB::raw('lower(code)'), '=', $code)->first();
                    if ($promo->partner()->exists()) {
                        $partner = $promo->partner->id;
                    }
                    $discount = $promocode['promocode_discount'];
                    $data['promo'] = $request->promo;
                    $data['discount'] = $discount;
                    $data['promocode']['code'] = $request->promocode;
                    if (!empty($promocode['discount_cart'])) {
                        $data['promocode']['discount_cart'] = $promocode['discount_cart'];
                    }
                } elseif ($promocode['error']) {
                    unset($request->promocode);
                    unset($request->discount);
                    unset($request->bonues);
                }
            } elseif ($request->voucher && !$request->promocode && !$request->bonuses) {
                $voucher = $this->checkVoucher($request);
                if (isset($voucher['voucher_discount'])) {
                    $discount = $voucher['voucher_discount'];
                    $data['promo'] = $request->promo;
                    $data['discount'] = $discount;
                    $data['voucher']['code'] = $request->voucher;
                } elseif ($voucher['error']) {
                    unset($request->voucher);
                    unset($request->discount);
                    unset($request->bonues);
                }
            } elseif ($request->bonuses && !$request->voucher && !$request->promocode) {
                if ($userBonuses > 0) {
                    $discount = $userBonuses;
                    $data['discount'] = $discount;
                    $data['bonuses'] = true;
                    // золотой час diamondPromo2
                    if (getSettings('diamondPromo2')) {
                        $data['super_bonuses'] = $user->getSuperBonuses();
                    }
                } else {
                    unset($request->voucher);
                    unset($request->discount);
                    unset($request->bonues);
                }
            }
        }
        // добавляем реферала по промокоду
        if (!$partner && $request->cookie('partner')) {
            $partner = $request->cookie('partner');
        }
        // проверяем корзину
        $data_cart = [];
        $total = 0;
        $price_for_discount = 0;
        foreach ($cart->content() as $item) {
            if ($item->qty > 0) {
                $product = Product::dontCache()->select(
                    'id',
                    'sku',
                    'name',
                    'quantity',
                    'status',
                    'price',
                    'options',
                    'data_status',
                    'data_quantity',
                    'product_options',
                    'options->coupones as coupones',
                    'preorder'
                )->where('id', '=', $item->options->product_id)->first();
                // если это не подарок, проверяем наличие
                $stock = $product->getStock($request['shipping-code'], $item->qty);
                if (!$stock) {
                    return back()->withInput()->withErrors([
                        'message' => 'Некоторых товаров в вашей корзине нет в наличии'
                    ]);
                }
                //        if(!$item->options->gift&&!$item->price){
                if (!$product->price) {
                    return back()->withInput()->withErrors([
                        'message' => 'Некоторые товары недоступны для покупки'
                    ]);
                }
                $new_item = [
                    'id' => $item->options->product_id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'model' => $item->id,
                    'image' => $item->options->image,
                ];
                if ($item->options->parent_product) {
                    $new_item['parent_product'] = $item->options->parent_product;
                }
                if ($item->options->old_price) {
                    $new_item['old_price'] = $item->options->old_price;
                }
                if ($product->preorder) {
                    $data['preorder'] = true;
                    $new_item['preorder'] = true;
                }
                $data_cart[] = $new_item;
                $total += $item->price * $item->qty;
                if ($item->options->discount_does_not_apply != 1) {
                    $price_for_discount += $item->price * $item->qty;
                }
            }
        }
        if (empty($data_cart)) {
            return back()->withErrors(['Ошибка']);
        }
        $data['total'] = $total;
        $shipping_price = $request['shipping-price'] ?? 0;
        // доставка
        $pickup = null;
        $shipping_code = $request['shipping-code'];
        if ($shipping_code == 'nt') {
            $shipping_code = 'cdek';
        }
        $shipping_method = ShippingMethod::where('code', $shipping_code)->first();
        $shipping_method_name = null;
        if (!$shipping_method) {
            $pickup = Pickup::select('code', 'name', 'params')->where('code', $request['shipping-code'])->first();
            $shipping_method_name = $pickup->name;
        } else {
            $shipping_method_name = $shipping_method->name;
        }
        $country_id = $request->country;
        if ($pickup) {
            $country_id = 1;
        }
        $country = Country::find($country_id);
        if (!$country) {
            return back()->withErrors(['Выберите страну.']);
        }
        $country_code = $country->options['pochta_code'];

        $lm_country_id = null;
        $lm_region_id = null;
        $lm_city_id = null;
        $data_shipping = [
            'shipping-code' => $shipping_code,
            'shipping-method' => $shipping_method_name,
            'country_code' => $country_code,
            'price' => $shipping_price
        ];
        if (!isset($country)) {
            $country = Country::find($request->country);
            $lm_country_id = $country->lm_country_id;
        }

        if ($shipping_code == 'cdek') {
            $data_shipping['cdek-pvz-id'] = $request['cdek-pvz-id'] ?? $request['nt-pvz-id'];
            $data_shipping['cdek-pvz-address'] = $request['cdek-pvz-address'] ?? $request['nt-pvz-address'];

            $pvz = CdekPvz::query()->where('code', $data_shipping['cdek-pvz-id'])->first();
            $cdek_city = $pvz->cdek_city;
            if ($cdek_city) {
                $lm_country_id = $cdek_city->lm_country_id;
                $lm_region_id = $cdek_city->lm_region_id;
                $lm_city_id = $cdek_city->lm_city_id;
            }
        } elseif ($shipping_code == 'cdek_courier') {
            $data_shipping['cdek_courier-form-region'] = $request['cdek_courier-form-region'];
            $data_shipping['cdek_courier-form-city'] = $request['cdek_courier-form-city'];
            $data_shipping['cdek_courier-form-street'] = $request['cdek_courier-form-street'];
            $data_shipping['cdek_courier-form-house'] = $request['cdek_courier-form-house'];
            $data_shipping['cdek_courier-form-flat'] = $request['cdek_courier-form-flat'] ?? null;
            $data_shipping['cdek_courier-form-address'] = $request['cdek_courier-form-address'];
            $cdek_city = CdekCity::query()->where('code', $request['cdek_courier-form-city'])->first();
            if ($cdek_city) {
                $lm_country_id = $cdek_city->lm_country_id;
                $lm_region_id = $cdek_city->lm_region_id;
                $lm_city_id = $cdek_city->lm_city_id;
            }
        } elseif ($shipping_code == 'yandex') {
            $data_shipping['yandex-pvz-id'] = $request['yandex-pvz-id'];
            $data_shipping['yandex-pvz-address'] = $request['yandex-pvz-address'];

            $pvz = BoxberryPvz::query()->where('code', $request['yandex-pvz-id'])->first();
            $yandex_city = $pvz->city;
            if ($yandex_city) {
                $lm_country_id = $yandex_city->lm_country_id;
                $lm_region_id = $yandex_city->lm_region_id;
                $lm_city_id = $yandex_city->lm_city_id;
            }
        } elseif ($shipping_code == 'x5post') {
            $data_shipping['x5post-pvz-id'] = $request['x5post-pvz-id'];
            $data_shipping['x5post-pvz-address'] = $request['x5post-pvz-address'];

            //      $pvz = X5PostPvz::query()->where('mdmCode', $request['x5post-pvz-id'])->first();
            //      $x5post_city = $pvz->city;
            //      if($x5post_city){
            //        $lm_country_id = $x5post_city->lm_country_id;
            //        $lm_region_id = $x5post_city->lm_region_id;
            //        $lm_city_id = $x5post_city->lm_city_id;
            //      }
        } elseif ($shipping_code == 'pochta') {
            $data_shipping['country'] = $country->name;
            $data_shipping['postcode'] = $request['postcode'];
            $data_shipping['region'] = $request['region'];
            $data_shipping['city'] = $request['city'];
            $data_shipping['street'] = $request['street'];
            $data_shipping['house'] = $request['house'];
            $data_shipping['flat'] = $request['flat'];
            $data_shipping['full_address'] = $request['postcode'] . ' ' . $country->name . ', ' . $request['region'] . ', ' . $request['city'] . ', ' . $request['street'] . ', ' . $request['house'] . ' ' . $request['flat'];
        } elseif ($shipping_code == 'pickup') {
            $lm_country_id = 1;
            $lm_region_id = 66;
            $lm_city_id = 186;
        }
        $data['all_fields'] = $request->toArray();
        foreach ($data['all_fields'] as $key => $value) {
            if ($value === null) {
                unset($data['all_fields'][$key]);
            }
        }
        $slug = getCode(4);
        $result_total = $total + $shipping_price;
        if ($discount > 0) {
            if (isset($promocode['promocode_discount']) && $price_for_discount > 0) {
                if ($price_for_discount == $total) {
                    $result_total = $price_for_discount - $discount;

                    if ($result_total <= 0) { // Товар 0 рублей (символично ставим 1рубль)
                        $result_total = $shipping_price + 1;
                    } else {
                        $result_total += $shipping_price;
                    }
                } else {
                    $total -= $price_for_discount;
                    $result_total = $price_for_discount - $discount;

                    if ($result_total < 0) {
                        $result_total = 0;
                    }
                    $result_total += $total + $shipping_price;
                }
            } elseif (isset($voucher['voucher_discount'])) {
                $result_total = $total + $shipping_price - $discount;
                if ($result_total <= 0) {
                    $result_total = 1;
                }
            } elseif ($userBonuses > 0 && $request->bonuses) {
                $maxDiscountFromBunus = $price_for_discount / 2;
                $correctDiscount = ($maxDiscountFromBunus > $discount) ? $discount : $maxDiscountFromBunus;

                if ($price_for_discount == $total) {
                    $result_total -= $correctDiscount;
                    if ($result_total <= 0) { // если сумма 0 рублей (символично ставим 1рубль)
                        $result_total = 1;
                    }
                } else {
                    $result_total = $price_for_discount - $correctDiscount;

                    if ($result_total < 0) { // если сумма 0 рублей (символично ставим 1рубль)
                        $result_total = 1;
                    }
                }
            }
            $discount_total = $total + $shipping_price - $result_total;
            $data['discount'] = $discount_total;
        }
        $order = Order::create([
            'user_id' => $user->id,
            'data' => $data,
            'data_cart' => $data_cart,
            'data_shipping' => $data_shipping,
            'amount' => $result_total,
            'confirm' => 0,
            'slug' => $slug,
            'partner_id' => $partner ?? null,
            'country_id' => $lm_country_id ?? null,
            'region_id' => $lm_region_id ?? null,
            'city_id' => $lm_city_id ?? null,
        ]);
        $order->update([
            'slug' => $order->getOrderNumber() . '_' . $order->slug
        ]);
        // используем промокоды
        if ($discount > 0) {
            if (isset($promocode['promocode_discount']) && $price_for_discount > 0) {
                $coupone = Coupone::where('code', $request->promocode)->first();
                if ($coupone && $coupone->count <= 1) {
                    DB::table('coupones')
                        ->where('code', $request->promocode)
                        ->update([
                            'order_id' => $order->id,
                            'used_at' => now()->format('Y-m-d H:i:s')
                        ]);
                }
            } elseif (isset($voucher['voucher_discount'])) {
                DB::table('vouchers')
                    ->where('code', $request->voucher)
                    ->update([
                        'order_id' => $order->id,
                        'used_at' => now()->format('Y-m-d H:i:s')
                    ]);
            } elseif ($userBonuses > 0 && $request->bonuses) {
                // золотой час diamondPromo2
                if (getSettings('diamondPromo2')) {
                    $userSuperBonuses = $user->getSuperBonuses();

                    if ($discount_total - $userSuperBonuses > 0) {
                        $user->subSuperBonuses($discount_total - ($discount_total - $userSuperBonuses), 'Заказ ' . $order->getOrderNumber());
                        $user->subBonuses($discount_total - $userSuperBonuses, 'Заказ ' . $order->getOrderNumber());
                    } else {
                        $user->subSuperBonuses($discount_total, 'Заказ ' . $order->getOrderNumber());
                    }
                } else {
                    // обычный режим
                    $user->subBonuses($discount_total, 'Заказ ' . $order->getOrderNumber());
                }
            }
        }
        // обновить корзину
        $data_cart = $order->data_cart;
        $cart_ids = [];
        $cart_qty = [];
        foreach ($data_cart as $cart_item) {
            $orderItem = OrderItem::setParams($order->id, $cart_item);
            $cart_ids[] = $cart_item['id'];
            $cart_qty[$cart_item['id']] = $cart_item['qty'];
            if (isset($cart_item['parent_product'])) {
                $cart_ids[] = $cart_item['parent_product'];
                $cart_qty[$cart_item['parent_product']] = $cart_item['qty'];
            }
        }
        $products = Product::whereIn('id', $cart_ids)->get();
        foreach ($products as $product) {
            if (isset($pickup) && $pickup && isset($pickup->params['quantity'])) { // &&$product->id!=69){
                $quantity_field = 'data_quantity->' . $pickup->params['quantity'];
                DB::table('products')->where('products.id', $product->id)->decrement($quantity_field, $cart_qty[$product->id]);

                // для опций
                if ($product->product_id) {
                    DB::table('products')->where('products.id', $product->product_id)->decrement($quantity_field, $cart_qty[$product->id]);
                }
            } else {
                DB::update('UPDATE `products` SET `quantity`=`quantity`-' . $cart_qty[$product->id] . ' WHERE `id` = ' . $product->id . ';');
                // для опций
                if ($product->product_id) {
                    DB::update('UPDATE `products` SET `quantity`=`quantity`-' . $cart_qty[$product->id] . ' WHERE `id` = ' . $product->product_id . ';');
                }
            }
        }
        Product::flushQueryCache();
        if (getSettings('paymentMethod') == 'Cloudpayments') {
            return redirect()->route('order.cloudpayments', $order->slug);
        } else {
            return redirect()->route('order.robokassa', $order->slug);
        }
    }

    public function finishOrder(User $user, Order $order)
    {
        $order_data = $order->data;
        $order_shipping = $order->data_shipping;
        if (isset($order_data['is_voucher']) && $order_data['is_voucher']) { // продажа подарочного сертификата
            $targetDate = Carbon::create(2024, 1, 2, 0, 0, 0);
            if (now()->lessThan($targetDate)) {
                foreach ($order->data_cart as $item) {
                    if ($item['id'] == 1104) {
                        $order->user->addBonuses(1000, 'Заказ ' . $order->getOrderNumber());
                        break;
                    }
                }
            }
            (new VoucherController)->createVouchers($order);
        } elseif (isset($order_data['is_meeting']) && $order_data['is_meeting']) {
            (new MailSender($user->email))->confirmMeetingOrder($order, $user);
            foreach ($user->tgChats as $tgChat) {
                (new TelegramSender($tgChat))->confirmMeetingOrder($order, $user);
            }
        } else {
            // обнуляем промокод или подарочный сертификат
            $discount = $order_data['discount'] ?? 0;
            if ($discount > 0 && $order_data['total'] + $order_shipping['price'] - $order->amount > 0) {
                if (isset($order_data['voucher'])) {
                    $voucher = $order->voucher()->where('code', $order_data['voucher']['code'])->first();
                    if ($voucher) {
                        $amount = $voucher->amount - ($order_data['total'] + $order_shipping['price'] - $order->amount);
                        $voucher_params = [
                            'amount' => $amount
                        ];
                        if ($amount > 0 && $voucher->save_amount) {
                            $voucher_params['order_id'] = null;
                            $voucher_params['used_at'] = null;
                        }
                        $voucher->update($voucher_params);
                        VoucherTransactions::create([
                            'voucher_id' => $voucher->id,
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'amount' => ($order_data['total'] + $order_shipping['price'] - $order->amount),
                        ]);
                    }
                } elseif (isset($order_data['promocode'])) {
                    $promocode = $order->promocode()->where('code', $order_data['promocode']['code'])->first();
                    if ($promocode) {
                        $promocode->update([
                            'count' => $promocode->count - 1
                        ]);
                    }
                }
            }
            if (getSettings('diamondPromo1') && !($order->data['is_voucher'] ?? false)) {
                $order->user->addSuperBonuses($order->data['total'] * 0.7, 'Заказ ' . $order->getOrderNumber());
            }

            if ((getSettings('promo20') || getSettings('happyCoupon')) && !($order->data['is_voucher'] ?? false)) {
                //        if($order->data['total'] >= 10000){
                //          $order->user->addBonuses(1500);
                //        }
                if ($order_data['total'] >= 3599) {
                    (new HappyCoupon())->setPrizeToOrder($order);
                }
                //
                //        $data_cart = $order->data_cart;
                //        $cart_items = 0;
                //        foreach($data_cart as $item){
                //          if($item['model'] == 'pzl'){
                //            continue;
                //          }
                //          $cart_items += $item['qty'];
                //        }
                //        if ($cart_items >= 3){
                //          (new HappyCoupon())->setPrizeToOrder($order);
                //        }
            }

            if (getSettings('catInBag') && !($order->data['is_voucher'] ?? false)) {
                $catInBagParticipated = true;
                $visibleCategoryIds = $order_data['cat_in_bag_visible_category_ids'] ?? [];
                if (empty($visibleCategoryIds) && $order->user_id) {
                    $preview = CatInBagPreview::query()
                        ->where('user_id', $order->user_id)
                        ->where('expires_at', '>', now())
                        ->orderByDesc('id')
                        ->first();
                    $visibleCategoryIds = $preview?->category_ids ?? [];
                }

                $goodsTotal = (int)($order_data['total'] ?? 0);
                $cartTotal = 0;
                $parsePrice = static function ($value): int {
                    if (is_int($value)) {
                        return $value;
                    }
                    if (is_float($value)) {
                        return (int)round($value);
                    }
                    if (is_numeric($value)) {
                        return (int)round((float)$value);
                    }
                    $normalized = str_replace([' ', '₽'], '', (string)$value);
                    $normalized = str_replace(',', '.', $normalized);
                    return (int)round((float)$normalized);
                };
                if (!empty($order->data_cart) && is_array($order->data_cart)) {
                    foreach ($order->data_cart as $item) {
                        $price = $parsePrice($item['price'] ?? 0);
                        $qty = (int)($item['qty'] ?? 1);
                        $cartTotal += $price * $qty;
                    }
                }
                $goodsTotal = max($goodsTotal, $cartTotal);
                if ($goodsTotal <= 0 && isset($order->amount)) {
                    $goodsTotal = (int)$order->amount;
                }

                if ($catInBagParticipated && $goodsTotal >= 4000 && count($visibleCategoryIds) >= 2) {
                    (new GameService())->createSession($goodsTotal, $order->id, $order->user_id, $visibleCategoryIds);
                }
            }
            $coupon = $order->coupon()->first();
            if (getSettings('promo_1+1=3') && !$coupon) {
                Promo113::gift($order);
            }
            if ($user->is_subscribed_to_marketing) {
                (new MailSender($user->email))->confirmOrder($order, $user);
                foreach ($user->tgChats as $tgChat) {
                    (new TelegramSender($tgChat))->confirmOrder($order, $user);
                }
            }

            if (getSettings('payment_test')) {
                $order->setStatus('test');
            } else {
                $order->setStatus('is_processing');
            }
        }
    }

    public function success_page(Request $request)
    {
        $order = Order::find($request->InvId);
        $template = 'template.public.order.success';
        $coupon = null;
        if ($order) {
            if ($order->confirm) {
                //        $created_at = Carbon::parse($order->created_at);
                //        $threshold = Carbon::create(2024, 12, 25, 10, 0);
                //
                //        if ($created_at->gt($threshold)) {
                //          $template = 'template.public.order.successWishes';
                //        }
                $message = [
                    'title' => 'Спасибо за оплату заказа',
                    'text' => 'Номер вашего заказа ' . $order->getOrderNumber() . '.',
                    'text-confirm-email' => 'Все инструкции отправлены на ваш email, указанный при оформлении заказа.'
                ];
                $coupon = $order->coupon()->first();
            } else {
                $message = [
                    'title' => 'Спасибо за оплату заказа',
                    'text' => 'Номер вашего заказа ' . $order->getOrderNumber() . '.<br/>Вы получите сообщение на почту, как только ваш платеж будет подтвержден'
                ];
            }
        } else {
            $message = [
                'title' => 'Спасибо за оплату заказа',
                'text' => 'Вы получите сообщение на почту, как только ваш платеж будет подтвержден'
            ];
        }

        $seo['title'] = 'Спасибо за оплату заказа';
        return view($template, compact('seo', 'message', 'order', 'coupon'));
    }

    public function fail_page(Request $request)
    {
        if ($request->InvId) {
            $order = Order::find($request->InvId);
            if ($order) {
                return redirect()->route('order.cloudpayments', $order->slug);
            }
        }
        $message = [
            'title' => 'К сожалению, платеж не прошел',
            'text' => 'Мы не смогли принять оплату. Пожалуйста, попробуйте снова или обратитесь в техническую поддержку'
        ];
        $seo['title'] = 'К сожалению, платеж не прошел';
        return view('template.public.order.fail', compact('seo', 'message'));
    }

    public function checkVoucher(Request $request)
    {
        $request->validate([
            'voucher' => ['required', 'string']
        ]);
        $code = $request->voucher;
        $voucher_result = false;
        Log::debug(print_r($code, true));
        if ($code) {
            $code = mb_strtolower($code);
            $voucher = Voucher::where(DB::raw('lower(code)'), '=', $code)
                ->where('amount', '>', 0)
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
                })
                ->first();
            //      if($code==mb_strtolower('388L-1250-3898')){
            //        dd($voucher);
            //      }
            if ($voucher) {
                if ($voucher->used_at && $voucher->used_at->gt(now()->subHour())) {
                    $request->session()->forget(['voucher']);
                    $request->session()->forget(['voucher_discount']);
                    $result = [
                        'error' => 'Данный сертификат уже применен в другом заказе'
                    ];
                    return $result;
                } else {
                    $voucher_result = $voucher->amount;
                }
            }
        }

        if ($voucher_result === false) {
            $request->session()->forget(['voucher']);
            $request->session()->forget(['voucher_discount']);
            $result = [
                'error' => 'Данный сертификат недействителен'
            ];
        } else {
            session([
                'voucher' => $code,
                'voucher_discount' => $voucher_result
            ]);
            $result = [
                'voucher_discount' => $voucher_result
            ];
            $request->session()->forget(['promocode']);
            $request->session()->forget(['discount_cart']);
            $request->session()->forget(['promocode_discount']);
        }
        return $result;
    }

    public function checkPromocode(Request $request)
    {
        $request->validate([
            'promocode' => ['required', 'string']
        ]);
        $code = $request->promocode;
        $promo_result = false;
        $discount = 0;
        $discount_cart = [];
        $cart = Cart::instance('cart');
        if (!empty($code)) {
            $code = mb_strtolower($code);
            $promo = Coupone::where(DB::raw('lower(code)'), '=', $code)
                ->where('amount', '>', 0)
                ->where('count', '>', 0)
                ->where(function ($query) {
                    $query->where('available_until', '>', date('Y-m-d H:i:s'))
                        ->orWhere('available_until', null);
                    return $query;
                })->first();
            if ($promo) {
                if ((getSettings('promo30') || getSettings('promo20') || getSettings('goldTicket') || getSettings('happyCoupon')) && !$promo->partner()->exists()) {
                    $error_message = 'Данный код недействителен в преиод проведения акции'; // "Счастливый купон"
                    $promo = null;
                    //        }elseif(!getSettings('happyCoupon')&&$promo->partner()->exists()){
                    //          $error_message = 'Данный код недействителен';
                    //          $promo = null;
                } elseif ($promo->used_at && $promo->used_at->gt(now()->subHour())) {
                    $error_message = 'Данный код уже применен в другом заказе';
                    $promo = null;
                }
            } else {
                $error_message = 'Данный код недействителен';
            }
            if ($promo && ($promo->options['min_cart'] ?? false)) {
                $min_cart = $promo->options['min_cart'];
                if ($cart->subtotal(0, '.', '') < $min_cart) {
                    $error_message = 'Данный код доступен при заказе от ' . $min_cart . ' рублей';
                    $promo = null;
                }
            }
            if ($promo) {

                $total_for_discount = 0;
                $promo_result = $promo->amount;

                if ($promo->type == 10) {
                    $total_for_discount = $cart->subtotal(0, '.', '');
                    $discount = $promo->amount;
                    $discount_cart = [];
                } else {
                    $cart->content()->sortBy('price')->reverse();
                    if ($promo->type == 3) {
                        $count = 3;
                    } elseif ($promo->type == 2) {
                        $count = 999;
                    } else {
                        $count = 1;
                    }
                    foreach ($cart->content() as $item) {
                        if ($count == 0) {
                            break;
                        }
                        if ($item->options->discount_does_not_apply) {
                            continue;
                        }
                        $total_for_discount += $item->price * $item->qty;
                        //Log::debug($total_for_discount.' - '.$item->id);
                        $item_price = (int)($item->price * ($promo_result / 100));
                        for ($i = 0; $i < $item->qty; $i++) {
                            $discount += $item_price;
                            if (!isset($discount_cart[$item->id])) {
                                $discount_cart[$item->id] = [
                                    'price' => $item->price - $item_price,
                                    'qty' => 0
                                ];
                            }
                            $discount_cart[$item->id]['qty']++;
                        }
                    }
                }
            }
        }

        if ($discount === 0) {
            $request->session()->forget(['promocode']);
            $request->session()->forget(['discount_cart']);
            $request->session()->forget(['promocode_discount']);
            $result = [
                'error' => $error_message ?? 'Данный код недействителен'
            ];
        } else {
            session([
                'promocode' => $code,
                'discount_cart' => $discount_cart,
                'promocode_discount' => $discount
            ]);
            $result = [
                'promocode' => $code,
                'discount_cart' => $discount_cart,
                'promocode_discount' => $discount,
                'total_for_discount' => $total_for_discount ?? $cart->subtotal(0, '.', ''),
                'seller' => $promo->partner->id ?? null
            ];
            $request->session()->forget(['voucher']);
            $request->session()->forget(['voucher_discount']);
        }
        return $result;
    }

    private function validateOrder(Request $request)
    {
        $cart = $this->cart;

        $validate = [];
        $validate['instagram'] = ['nullable', 'string', 'max:255'];
        $validate['first_name'] = ['required', 'string', 'max:255'];
        $validate['middle_name'] = ['nullable', 'string', 'max:255'];
        $validate['last_name'] = ['required', 'string', 'max:255'];
        $validate['email'] = ['required', 'string', 'email:rfc,dns', 'max:255', 'confirmed'];
        $validate['phone'] = ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'];

        $validate['oferta'] = ['required', 'string', 'max:30'];
        $validate['politika'] = ['required', 'string', 'max:30'];
        //    $validate['mailing'] = ['required', 'boo', 'max:30'];
        $validate['giftDelivery'] = ['required'];
        if (getSettings('promo20') || getSettings('happyCoupon') || getSettings('goldTicket')) {
            $validate['giftPolitika'] = ['required', 'string', 'max:30'];
            //      $validate['gift-delivery'] = ['required', 'string', 'max:30'];
            //
        }
        //    $validate['goldpromo'] = ['required', 'string', 'max:30'];
        if (getSettings('promo_1+1=3')) {
            $validate['promo113'] = ['required', 'string', 'max:30'];
            //      $validate['gift-delivery'] = ['required', 'string', 'max:30'];
        }
        if (getSettings('puzzlesStatus')) {
            $validate['promoPzl'] = ['required', 'string', 'max:30'];
        }
        $shipping = $cart->content()->filter(function ($cartItem) {
            return !$cartItem->options->has('shipping') || $cartItem->options->shipping != false;
        })->count();
        if ($shipping) {
            $validate['country'] = ['required'];
            if (getSettings('promo_1+1=3')) {
                $validate['shipping-price'] = ['required', 'numeric', 'min:250'];
            }
            if ($request['shipping-code'] == 'cdek_courier') {
                $validate['shipping-price'] = ['required', 'numeric'];
            }
            if (getSettings('goldTicket')) {
                $setCount = $cart->content()->sum(function ($item) {
                    return $item->options->category_id == 33;
                });
                if ($setCount) {
                    $validate['shipping-price'] = ['required', 'numeric'];
                }
            } else {
                $count = $cart->content()->sum(function ($item) {
                    return $item->options->product_id != 1125 ? $item->qty : 0;
                });
                if ($count >= 3) {
                    $validate['shipping-price'] = ['required', 'numeric'];
                }
            }




            $shipping_methods = ShippingMethod::select('code', 'name')->where('active', true)->get();
            $shipping_codes = 'in:' . implode(',', $shipping_methods->pluck('code')->toArray()) . ',';

            $pickups = Pickup::select('code', 'name', 'address', 'params')->order()->pluck('code')->toArray();
            $shipping_codes .= implode(',', $pickups);
            $shipping_codes = trim($shipping_codes, ',');
            $validate['shipping-code'] = ['required', 'string', $shipping_codes];
            $shipping_code = $request['shipping-code'];

            if ($request->country == 1) { // russia

                if ($shipping_code == 'cdek') {
                    $validate['cdek-pvz-id'] = ['required', 'string', 'max:255'];
                    $validate['cdek-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'nt') {
                    $validate['nt-pvz-id'] = ['required', 'string', 'max:255'];
                    $validate['nt-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'cdek_courier') {
                    $validate['cdek_courier-form-region'] = ['required', 'string', 'max:30'];
                    $validate['cdek_courier-form-city'] = ['required', 'string', 'max:60'];
                    $validate['cdek_courier-form-street'] = ['required', 'string', 'max:255'];
                    $validate['cdek_courier-form-house'] = ['required', 'string', 'max:255'];
                    $validate['cdek_courier-form-flat'] = ['nullable', 'string', 'max:255'];
                    $validate['cdek_courier-form-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'yandex') {
                    $validate['yandex-pvz-id'] = ['required', 'string', 'max:255'];
                    $validate['yandex-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'x5post') {
                    $validate['x5post-pvz-id'] = ['required', 'string', 'max:255'];
                    $validate['x5post-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'pochta') {
                    if ($request['country'] != 0) {
                        $validate['shipping-agry'] = ['nullable', 'string', 'max:10'];
                    }
                    $validate['postcode'] = ['required', 'string', 'max:8'];
                    $validate['region'] = ['required', 'string', 'max:180'];
                    $validate['city'] = ['required', 'string', 'max:180'];
                    $validate['street'] = ['required', 'string', 'max:255'];
                    $validate['house'] = ['required', 'string', 'max:10'];
                    $validate['flat'] = ['nullable', 'string', 'max:10'];
                } elseif (in_array($request['shipping-code'], $pickups)) {
                    unset($validate['shipping-price']);
                }
            } else { // world
                $country = Country::find($request->country);
                if (!$country) {
                    return back()->withErrors(['Выберите страну']);
                }
                $allowed_shipping_methods = $country->options['status'];
                $validate['shipping-code'] = ['required', 'string', 'in:' . implode(',', $allowed_shipping_methods)];
                if ($shipping_code == 'cdek') {
                    //$validate['shipping-code'] = ['required', 'string', 'in:cdek'];
                    $validate['cdek-pvz-id'] = ['required', 'string', 'max:30'];
                    $validate['cdek-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'nt') {
                    $validate['nt-pvz-id'] = ['required', 'string', 'max:30'];
                    $validate['nt-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'cdek_courier') {
                    $validate['cdek_courier-form-region'] = ['required', 'string', 'max:30'];
                    $validate['cdek_courier-form-city'] = ['required', 'string', 'max:30'];
                    $validate['cdek_courier-form-street'] = ['required', 'string', 'max:255'];
                    $validate['cdek_courier-form-house'] = ['required', 'string', 'max:255'];
                    $validate['cdek_courier-form-flat'] = ['nullable', 'string', 'max:255'];
                    $validate['cdek_courier-form-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'yandex') {
                    $validate['yandex-pvz-id'] = ['required', 'string', 'max:30'];
                    $validate['yandex-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'x5post') {
                    $validate['x5post-pvz-id'] = ['required', 'string', 'max:30'];
                    $validate['x5post-pvz-address'] = ['required', 'string', 'max:255'];
                } elseif ($shipping_code == 'pochta') {
                    $validate['shipping-agry'] = ['required', 'string', 'max:10'];
                    $validate['postcode'] = ['required', 'string', 'max:10'];
                    $validate['region'] = ['required', 'string', 'max:180'];
                    $validate['city'] = ['required', 'string', 'max:180'];
                    $validate['street'] = ['required', 'string', 'max:255'];
                    $validate['house'] = ['required', 'string', 'max:10'];
                    $validate['flat'] = ['nullable', 'string', 'max:10'];
                }
            }
        }
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
            'mailing.required' => 'Примите соглашение на получение рассылок',
            'gift-politika.required' => 'Примите условия акции',
            'gift-delivery.required' => 'Примите сроки доставки заказа',
            'promo113.required' => 'Примите правила проведения рекламной акции «1+1=3»',
            'promoPzl.required' => 'Примите правила проведения рекламной акции «Собери картину»',
            'country.required' => 'Выберите страну доставки',
            'shipping-price.required' => 'Стоимость доставки не рассчитана',
            'shipping-price.min' => 'Стоимость доставки не рассчитана',
            'shipping-price.numeric' => 'Стоимость доставки не рассчитана',
            'shipping-code.required' => 'Не выбран способ доставки',
            'shipping-code.in' => 'Выбран недоступный способ доставки',
            'cdek-pvz-id.required' => 'Не выбран пункт выдачи заказов СДЭК',
            'cdek-pvz-address.required' => 'Не выбран пункт выдачи заказов СДЭК',
            'yandex-pvz-id.required' => 'Не выбран пункт выдачи заказов Яндекс',
            'yandex-pvz-address.required' => 'Не выбран пункт выдачи заказов Яндекс',
            'x5post-pvz-id.required' => 'Не выбран пункт выдачи заказов 5 Пост',
            'x5post-pvz-address.required' => 'Не выбран пункт выдачи заказов 5 Пост',
            'shipping-agry.required' => 'Примите условия международной доставки почтой',
            'postcode.required' => 'Укажите почтовый индекс',
            'region.required' => 'Укажите регион или область',
            'city.required' => 'Укажите город',
            'street.required' => 'Укажите улицу',
            'house.required' => 'Укажите дом',
        ];
        $request->validate($validate, $validate_exceptions);
        return true;
    }

    public function findNotPaidOrders()
    {
        $twentyMinutesAgo = Carbon::now()->subMinutes(20);
        Order::query()
            ->with('user')
            ->whereNull('status')
            ->where('confirm', 0)
            // ->where('user_id', 1)
            ->whereBetween('created_at', [$twentyMinutesAgo, $twentyMinutesAgo->copy()->addMinute()])
            ->get()
            ->each(function ($order) {
                (new MailSender($order->user->email))->orderNotification($order);
                foreach ($order->user->tgChats as $tgChat) {
                    (new TelegramSender($tgChat))->orderNotification($order);
                }
            });
    }
}
