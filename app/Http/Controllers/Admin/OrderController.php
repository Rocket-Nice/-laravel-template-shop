<?php

namespace App\Http\Controllers\Admin;

use App\Exports\OrdersExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Shipping\BoxberryController;
use App\Http\Controllers\Shipping\CdekController;
use App\Http\Controllers\Shipping\PochtaController;
use App\Http\Controllers\Shipping\X5PostController;
use App\Jobs\CheckOrdersStatusJob;
use App\Jobs\ExportOrdersJob;
use App\Jobs\getBoxberryTicketsJob;
use App\Jobs\getCdekTicketsJob;
use App\Jobs\getX5PostTicketsJob;
use App\Models\BoxberryPvz;
use App\Models\CdekCity;
use App\Models\CdekPvz;
use App\Models\Country;
use App\Models\ExportFile;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Partner;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\Setting;
use App\Models\ShippingLog;
use App\Models\ShippingMethod;
use App\Models\Status;
use App\Models\User;
use App\Models\X5PostPvz;
use App\Jobs\SendOrdersToBoxberryJob;
use App\Jobs\SendOrdersToCdekJob;
use App\Jobs\SendOrdersToPochtaJob;
use App\Jobs\SendOrdersToX5PostJob;
use App\Services\MailSender;
use App\Services\Smsru\Sender;
use App\Services\TelegramSender;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SafeObject;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:Редактирование заказов'])->only(['edit', 'update']);
        $this->middleware(['permission:Создание заказов'])->only(['create', 'store']);
    }

    public function index(Request $request)
    {
        $pickups = Pickup::select('id', 'code', 'name', 'params->role as permission')->get();
        $city_permissions = implode('|', $pickups->pluck('permission')->toArray());
        $orders = Order::select('id', 'user_id', 'data->double as double', 'data->copied as copied', 'data->form->full_name as full_name', 'data->preorder as preorder', 'data->comment as comment', 'data->form->phone as phone', 'data->form->email as email', 'data_shipping', 'data_cart', 'confirm', 'amount', 'slug', 'status', 'created_at')->filtered(new SafeObject($request->toArray()), $city_permissions, $pickups);
        //    if(request()->show_cart){
        //      $orders = Order::select('id', 'user_id', 'data->double as double', 'data->copied as copied', 'data->form->full_name as full_name', 'data->form->phone as phone', 'data->form->email as email', 'data_shipping', 'data_cart', 'confirm', 'amount', 'slug', 'status')->filtered($city_permissions, $pickups);
        //    }else{
        //      $orders = Order::select('id', 'user_id', 'data->double as double', 'data->copied as copied', 'data->form->full_name as full_name', 'data->form->phone as phone', 'data->form->email as email', 'data_shipping', 'confirm', 'amount', 'slug', 'status')->filtered($city_permissions, $pickups);
        //    }
        if (request()->orderBy && strpos(request()->orderBy, '|') !== false) {
            $orderBy = explode('|', request()->orderBy);
        }
        $orders = $orders->orderBy($orderBy[0] ?? 'id', $orderBy[1] ?? 'desc')->paginate(50);

        $products = Product::select('id', 'name', 'sku')
            ->where(function ($query) {
                $query->whereIn('type_id', [1, 2, 5, 6, 8, 9]);
                $query->orWhere('id', 1185);
            })
            ->get();
        $referrers = null;
        if (auth()->user()->hasRole('admin')) {
            $referrers = Partner::select('id', 'name')->orderBy('created_at', 'desc')->get();
        }


        $statuses = Status::query()->get();
        foreach ($statuses as $key => $status) {
            if (!Order::query()->where('status', $status->key)->exists()) {
                unset($statuses[$key]);
            }
        }

        $seo = [
            'title' => 'Все заказы'
        ];
        if (request()->user_id) {
            $user = User::select('name', 'email')->where('id', request()->user_id)->first();
            if ($user) {
                $seo = [
                    'title' => 'Заказы пользователя ' . $user->name
                ];
            }
        }
        return view('template.admin.orders.index', compact('orders', 'pickups', 'products', 'referrers', 'city_permissions', 'seo', 'statuses'));
    }

    public function show(Order $order)
    {
        $options = $order->data;
        $options_shipping = $order->data_shipping;

        $pickup = Pickup::where('code', $options_shipping['shipping-code'])->first();

        if ($options_shipping['shipping-code'] == 'cdek') {
            $cdek_pvz = CdekPvz::where('code', $options_shipping['cdek-pvz-id'])->first();
            if ($cdek_pvz) {
                $options_shipping['region'] = $cdek_pvz->region;
                $options_shipping['city'] = $cdek_pvz->city;
            }
        }
        $order->data_shipping = $options_shipping;
        $cart = $order->data_cart;
        if ($cart && is_array($cart)) {
            foreach ($cart as $key => $item) {
                $product = Product::query()->select('volume')->where('id', $item['id'])->first();
                $cart[$key]['volume'] = $product->volume;
            }
        }
        $order->data_cart = $cart;
        $seo = [
            'title' => 'Заказ ' . $order->getOrderNumber()
        ];
        return view('template.admin.orders.show', compact('order', 'seo', 'pickup'));
    }

    public function create()
    {
        $countries = Country::orderAvailable()->orderBy('name', 'asc')->get();
        foreach ($countries as $key => $country) {
            $c_options = $country->options;
            $countries[$key]->options = $c_options;
        }
        $shipping_methods = ShippingMethod::all();
        $products = Product::query()
            ->select('id', 'name', 'sku', 'price', 'category_id')
            ->whereIn('type_id', [1, 2, 5, 6, 8])
            ->orWhere('id', 1186)
            ->orderBy('name', 'asc')
            ->get();
        $pickups = Pickup::select('code', 'name', 'address', 'params')->order()->get();
        $seo = [
            'title' => 'Создать заказ'
        ];

        return view('template.admin.orders.create', compact('seo', 'countries', 'shipping_methods', 'products', 'pickups'));
    }

    public function store(Request $request)
    {
        $validate = [];

        $validate['email'] = ['required', 'string', 'exists:users,email', 'max:255'];

        $validate['shipping'] = ['required', 'string', 'in:cdek,cdek_courier,yandex,x5post,bxb,ozon,pickup,pochta,none'];
        if ($request['shipping'] == 'cdek') {
            $validate['cdek-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['cdek-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'cdek_courier') {
            $validate['cdek_courier-form-region'] = ['required', 'string', 'max:30'];
            $validate['cdek_courier-form-city'] = ['required', 'string', 'max:30'];
            $validate['cdek_courier-form-street'] = ['required', 'string', 'max:255'];
            $validate['cdek_courier-form-house'] = ['required', 'string', 'max:255'];
            $validate['cdek_courier-form-flat'] = ['nullable', 'string', 'max:255'];
            $validate['cdek_courier-form-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'ozon') {
            $validate['ozon-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['ozon-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'yandex') {
            $validate['yandex-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['yandex-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'x5post') {
            $validate['x5post-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['x5post-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'pochta') {
            $validate['country'] = ['required', 'string', 'max:8'];
            $validate['postcode'] = ['required', 'string', 'max:8'];
            $validate['city'] = ['required', 'string', 'max:180'];
            $validate['street'] = ['required', 'string', 'max:255'];
            $validate['house'] = ['required', 'string', 'max:10'];
            $validate['flat'] = ['nullable', 'string', 'max:10'];
        }
        $request->validate($validate);


        $email = str_replace(' ', '', strtolower(trim($request->email, ' ')));
        $user = User::select()->where(DB::raw('lower(email)'), '=', $email)->first();
        if (!$user) {
            return back()->withInput()->withErrors([
                'message' => 'Пользователь не найден'
            ]);
        }
        $data = [
            'manual_mode' => true,
            'total' => 0,
            'form' => [
                'email' => $user->email,
                'phone' => $user->phone,
                'full_name' => $user->name,
                'last_name' => $user->last_name,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
            ]
        ];

        // проверяем корзину
        $data_cart = [];
        $total = 0;
        if (isset($request['cart'])) {
            $options_cart = $request['cart'];
            foreach ($options_cart as $key => $item) {
                $data_cart[] = $item;
                $total += $item['price'] * $item['qty'];
            }
        }

        $data['total'] = $total;
        $shipping_price = 0;
        // доставка
        if ($request['shipping-code'] == 'none') {
            $data_shipping = [
                'shipping-code' => $request['shipping-code'],
                'shipping-method' => 'Без доставки',
                'country_code' => null,
                'price' => 0
            ];
        } else {
            $pickup = null;
            $shipping_method = ShippingMethod::where('code', $request['shipping-code'])->first();
            $shipping_method_name = null;
            if (!$shipping_method) {
                $pickup = Pickup::select('code', 'name', 'params')->where('code', $request['shipping-code'])->first();
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
                'shipping-code' => $request['shipping-code'],
                'shipping-method' => $shipping_method_name,
                'country_code' => $country_code,
                'price' => $shipping_price
            ];
            if (!isset($country)) {
                $country = Country::find($request->country);
                $lm_country_id = $country->lm_country_id;
            }
            if ($request['shipping-code'] == 'cdek') {
                $data_shipping['cdek-pvz-id'] = $request['cdek-pvz-id'];
                $data_shipping['cdek-pvz-address'] = $request['cdek-pvz-address'];

                $pvz = CdekPvz::query()->where('code', $request['cdek-pvz-id'])->first();
                $cdek_city = $pvz->cdek_city;
                if ($cdek_city) {
                    $lm_country_id = $cdek_city->lm_country_id;
                    $lm_region_id = $cdek_city->lm_region_id;
                    $lm_city_id = $cdek_city->lm_city_id;
                }
            } elseif ($request['shipping-code'] == 'cdek_courier') {
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
            } elseif ($request['shipping-code'] == 'yandex') {
                $data_shipping['yandex-pvz-id'] = $request['yandex-pvz-id'];
                $data_shipping['yandex-pvz-address'] = $request['yandex-pvz-address'];

                $pvz = BoxberryPvz::query()->where('code', $request['yandex-pvz-id'])->first();
                $yandex_city = $pvz->city;
                if ($yandex_city) {
                    $lm_country_id = $yandex_city->lm_country_id;
                    $lm_region_id = $yandex_city->lm_region_id;
                    $lm_city_id = $yandex_city->lm_city_id;
                }
            } elseif ($request['shipping-code'] == 'x5post') {
                $data_shipping['x5post-pvz-id'] = $request['x5post-pvz-id'];
                $data_shipping['x5post-pvz-address'] = $request['x5post-pvz-address'];

                $pvz = X5PostPvz::query()->where('mdmCode', $request['x5post-pvz-id'])->first();
                $x5post_city = $pvz->city;
                if ($x5post_city) {
                    $lm_country_id = $x5post_city->lm_country_id;
                    $lm_region_id = $x5post_city->lm_region_id;
                    $lm_city_id = $x5post_city->lm_city_id;
                }
            } elseif ($request['shipping-code'] == 'pochta') {
                $data_shipping['country'] = $country->name;
                $data_shipping['postcode'] = $request['postcode'];
                $data_shipping['region'] = $request['region'];
                $data_shipping['city'] = $request['city'];
                $data_shipping['street'] = $request['street'];
                $data_shipping['house'] = $request['house'];
                $data_shipping['flat'] = $request['flat'];
                $data_shipping['full_address'] = $request['postcode'] . ' ' . $country->name . ', ' . $request['region'] . ', ' . $request['city'] . ', ' . $request['street'] . ', ' . $request['house'] . ' ' . $request['flat'];
            } elseif ($request['shipping-code'] == 'pickup') {
                $lm_country_id = 1;
                $lm_region_id = 66;
                $lm_city_id = 186;
            }
        }

        $data['all_fields'] = $request->toArray();
        foreach ($data['all_fields'] as $key => $value) {
            if ($value === null) {
                unset($data['all_fields'][$key]);
            }
        }
        $slug = getCode(4);
        $retult_total = $total + $shipping_price;

        $order = Order::create([
            'user_id' => $user->id,
            'data' => $data,
            'data_cart' => $data_cart,
            'data_shipping' => $data_shipping,
            'amount' => $retult_total,
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
        return redirect()->route('admin.orders.show', ['order' => $order->slug])->with([
            'status' => 'Заказ успешно создан'
        ]);
    }

    public function edit(Order $order)
    {
        $countries = Country::orderAvailable()->orderBy('name', 'asc')->get();
        foreach ($countries as $key => $country) {
            $c_options = $country->options;
            $countries[$key]->options = $c_options;
        }
        $shipping_methods = ShippingMethod::all();
        $pickups = Pickup::select('code', 'name', 'address', 'params')->order()->get();
        $seo = [
            'title' => 'Изменить заказ ' . $order->getOrderNumber()
        ];

        return view('template.admin.orders.edit', compact('order', 'seo', 'countries', 'shipping_methods', 'pickups'));
    }

    public function update(Order $order, Request $request)
    {
        $validate = [];
        $validate['first_name'] = ['required', 'string', 'max:30'];
        $validate['middle_name'] = ['nullable', 'string', 'max:30'];
        $validate['last_name'] = ['required', 'string', 'max:30'];
        $validate['email'] = ['required', 'string', 'email:rfc,dns', 'max:255'];
        $validate['phone'] = ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'];

        $validate['shipping'] = ['required', 'string', 'in:cdek,cdek_courier,yandex,x5post,bxb,ozon,pickup,pochta'];
        if ($request['shipping'] == 'cdek') {
            $validate['cdek-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['cdek-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'cdek_courier') {
            $validate['cdek_courier-form-region'] = ['required', 'string', 'max:30'];
            $validate['cdek_courier-form-city'] = ['required', 'string', 'max:30'];
            $validate['cdek_courier-form-street'] = ['required', 'string', 'max:255'];
            $validate['cdek_courier-form-house'] = ['required', 'string', 'max:255'];
            $validate['cdek_courier-form-flat'] = ['nullable', 'string', 'max:255'];
            $validate['cdek_courier-form-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'ozon') {
            $validate['ozon-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['ozon-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'yandex') {
            $validate['yandex-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['yandex-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'x5post') {
            $validate['x5post-pvz-id'] = ['required', 'string', 'max:30'];
            $validate['x5post-pvz-address'] = ['required', 'string', 'max:255'];
        } elseif ($request['shipping'] == 'pochta') {
            $validate['country'] = ['required', 'string', 'max:8'];
            $validate['postcode'] = ['required', 'string', 'max:8'];
            $validate['city'] = ['required', 'string', 'max:180'];
            $validate['street'] = ['required', 'string', 'max:255'];
            $validate['house'] = ['required', 'string', 'max:10'];
            $validate['flat'] = ['nullable', 'string', 'max:10'];
        }
        $request->validate($validate);

        $options = $order->data;
        $options_shipping = $order->data_shipping;
        $order_user = $order->user;
        $request->phone = preg_replace("/[^,.0-9]/", '', $request->phone);
        if (isset($request->country) && $request->country == 1) {
            $request->phone = preg_replace('/^8/', '+7', $request->phone);
            if ($request->phone[0] == '9') {
                $request->phone = '+7' . $request->phone;
            }
        }
        // ищем или создаем пользователя
        $email = strtolower($request->email);
        $phone = strtolower($request->phone);
        $name = $request->last_name . ' ' . $request->first_name;
        if (isset($request->middle_name) && !empty($request->middle_name)) {
            $name .= ' ' . $request->middle_name;
        }
        $user = User::select('id')->where(DB::raw('lower(email)'), '=', $email)->first();

        if ($request->change_user) {
            if ($user && $user->id != $order_user->id) {
                return back()->withInput()->withErrors([
                    'message' => 'Данный email уже используется'
                ]);
            }
            $user_params = [
                'phone' => $phone,
                'email' => $email
            ];
            if ($order_user->name != $name) {
                $user_params['name'] = $name;
            }
            $order_user->update($user_params);
        }
        if ($options['form']['last_name'] != $request->last_name) {
            $options['form']['last_name'] = $request->last_name;
        }
        if ($options['form']['first_name'] != $request->first_name) {
            $options['form']['first_name'] = $request->first_name;
        }
        if ($options['form']['middle_name'] != $request->middle_name) {
            $options['form']['middle_name'] = $request->middle_name;
        }
        if ($options['form']['full_name'] != $name) {
            $options['form']['full_name'] = $name;
        }
        if ($options['form']['email'] != $email) {
            $options['form']['email'] = $email;
        }
        if ($options['form']['phone'] != $phone) {
            $options['form']['phone'] = $phone;
        }
        // $shipping_methods = config('services.delivery');
        $pickup = null;
        $shipping_method = ShippingMethod::where('code', $request['shipping'])->first();
        $shipping_method_name = null;
        if (!$shipping_method) {
            $pickup = Pickup::select('code', 'name', 'params')->where('code', $request['shipping'])->first();
            $shipping_method_name = $pickup->name;
        } else {
            $shipping_method_name = $shipping_method->name;
        }
        // доставка
        $options_shipping = $options_shipping;
        if (!isset($options_shipping['shipping-code']) || $options_shipping['shipping-code'] != $request['shipping']) {
            $options_shipping['shipping-code'] = $request['shipping'];
            $options_shipping['shipping-method'] = $shipping_method_name;
        }
        $country = Country::find($request['country']);
        if (!isset($options_shipping['country_code']) || $options_shipping['country_code'] != $country->options['pochta_code']) {
            $options_shipping['country_code'] = $country->options['pochta_code'];
        }
        if ($request['shipping'] == 'cdek') {
            if (!isset($options_shipping['cdek-pvz-id']) || $options_shipping['cdek-pvz-id'] != $request['cdek-pvz-id']) {
                $options_shipping['cdek-pvz-id'] = $request['cdek-pvz-id'];
            }
            if (!isset($options_shipping['cdek-pvz-address']) || $options_shipping['cdek-pvz-address'] != $request['cdek-pvz-address']) {
                $options_shipping['cdek-pvz-address'] = $request['cdek-pvz-address'];
            }
        } elseif ($request['shipping'] == 'cdek_courier') {
            if (!isset($options_shipping['cdek_courier-form-region']) || $options_shipping['cdek_courier-form-region'] != $request['cdek_courier-form-region']) {
                $options_shipping['cdek_courier-form-region'] = $request['cdek_courier-form-region'];
            }
            if (!isset($options_shipping['cdek_courier-form-city']) || $options_shipping['cdek_courier-form-city'] != $request['cdek_courier-form-city']) {
                $options_shipping['cdek_courier-form-city'] = $request['cdek_courier-form-city'];
            }
            if (!isset($options_shipping['cdek_courier-form-street']) || $options_shipping['cdek_courier-form-street'] != $request['cdek_courier-form-street']) {
                $options_shipping['cdek_courier-form-street'] = $request['cdek_courier-form-street'];
            }
            if (!isset($options_shipping['cdek_courier-form-house']) || $options_shipping['cdek_courier-form-house'] != $request['cdek_courier-form-house']) {
                $options_shipping['cdek_courier-form-house'] = $request['cdek_courier-form-house'];
            }
            if (!isset($options_shipping['cdek_courier-form-flat']) || $options_shipping['cdek_courier-form-flat'] != $request['cdek_courier-form-flat'] ?? null) {
                $options_shipping['cdek_courier-form-flat'] = $request['cdek_courier-form-flat'] ?? null;
            }
            if (!isset($options_shipping['cdek_courier-form-address']) || $options_shipping['cdek_courier-form-address'] != $request['cdek_courier-form-address']) {
                $options_shipping['cdek_courier-form-address'] = $request['cdek_courier-form-address'];
            }
        } elseif ($request['shipping'] == 'yandex') {
            if (!isset($options_shipping['yandex-pvz-id']) || $options_shipping['yandex-pvz-id'] != $request['yandex-pvz-id']) {
                $options_shipping['yandex-pvz-id'] = $request['yandex-pvz-id'];
            }
            if (!isset($options_shipping['yandex-pvz-address']) || $options_shipping['yandex-pvz-address'] != $request['yandex-pvz-address']) {
                $options_shipping['yandex-pvz-address'] = $request['yandex-pvz-address'];
            }
        } elseif ($request['shipping'] == 'x5post') {
            if (!isset($options_shipping['x5post-pvz-id']) || $options_shipping['x5post-pvz-id'] != $request['x5post-pvz-id']) {
                $options_shipping['x5post-pvz-id'] = $request['x5post-pvz-id'];
            }
            if (!isset($options_shipping['x5post-pvz-address']) || $options_shipping['x5post-pvz-address'] != $request['x5post-pvz-address']) {
                $options_shipping['x5post-pvz-address'] = $request['x5post-pvz-address'];
            }
        } elseif ($request['shipping'] == 'pochta') {
            $country = Country::find($request->country);

            if (!isset($options_shipping['country']) || $options_shipping['country'] != $country->name) {
                $options_shipping['country'] = $country->name;
            }
            if (!isset($options_shipping['postcode']) || $options_shipping['postcode'] != $request['postcode']) {
                $options_shipping['postcode'] = $request['postcode'];
            }
            if (!isset($options_shipping['region']) || $options_shipping['region'] != $request['region']) {
                $options_shipping['region'] = $request['region'];
            }
            if (!isset($options_shipping['city']) || $options_shipping['city'] != $request['city']) {
                $options_shipping['city'] = $request['city'];
            }
            if (!isset($options_shipping['street']) || $options_shipping['street'] != $request['street']) {
                $options_shipping['street'] = $request['street'];
            }
            if (!isset($options_shipping['house']) || $options_shipping['house'] != $request['house']) {
                $options_shipping['house'] = $request['house'];
            }
            if (!isset($options_shipping['flat']) || $options_shipping['flat'] != $request['flat']) {
                $options_shipping['flat'] = $request['flat'];
            }
            $full_address = $request['postcode'] . ' ' . $country->name . ', ' . $request['region']  . ', ' . $request['city'] . ', ' . $request['street'] . ', ' . $request['house'] . ' ' . $request['flat'];
            if (!isset($options_shipping['full_address']) || $options_shipping['full_address'] != $full_address) {
                $options_shipping['full_address'] = $full_address;
            }
        }
        $options_shipping['old_data'] = $order->data_shipping;
        $options['old_data'] = $order->data;
        $options['edited_by'] = auth()->id();
        if (isset($options['old_data']['old_data'])) {
            unset($options['old_data']['old_data']);
        }

        // создаем заказ
        $order->update([
            'data' => $options,
            'data_shipping' => $options_shipping
        ]);
        $order->addLog('Изменены данные заказа', null, [
            'old' => [
                'data' => $order->data,
                'data_shipping' => $order->data_shipping,
            ],
            'new' => [
                'data' => $options,
                'data_shipping' => $options_shipping,
            ],
        ]);
        return redirect()->route('admin.orders.show', ['order' => $order->slug])->with([
            'status' => 'Данные успешно изменены'
        ]);
    }

    public function editCart(Order $order)
    {
        $seo = [
            'title' => 'Изменить заказ ' . $order->getOrderNumber()
        ];
        $products = Product::query()
            ->select('id', 'name', 'sku', 'price', 'category_id')
            ->whereIn('type_id', [1, 2, 5, 6, 8, 9])
            ->orWhere('id', 1186)
            ->orderBy('name', 'asc')
            ->get();
        return view('template.admin.orders.edit_cart', compact('order', 'seo', 'products'));
    }

    public function updateCart(Order $order, Request $request)
    {
        $options = $order->data;
        $old = $order->data_cart;
        $options_cart = $old;
        if (isset($request['cart'])) {
            $options_cart = $request['cart'];
            $new_cart = [];
            foreach ($options_cart as $key => $item) {
                if (isset($item['builder'])) {
                    $item['builder'] = json_decode($item['builder'], true);
                }
                $new_cart[] = $item;
            }
            $options['old_cart'][] = $order->data_cart;
            if (isset($options['old_cart']) && is_array($options['old_cart']) && count($options['old_cart']) > 2) {
                unset($options['old_cart'][0]);
            }
        }
        $options['cart_edited_by'] = auth()->id();
        // создаем заказ
        $order->update([
            'data' => $options,
            'data_cart' => $new_cart
        ]);

        // обновить корзину
        $order->items()->delete();
        $data_cart = $order->data_cart;
        foreach ($data_cart as $cart_item) {
            $orderItem = OrderItem::setParams($order->id, $cart_item);
            if (isset($cart_item['builder'])) {
                foreach ($cart_item['builder'] as $builder_item) {
                    $builderItem = OrderItem::setParams($order->id, $builder_item, $orderItem);
                }
            }
        }
        $order->addLog('Изменены данные корзины', null, [
            'old' => $old,
            'new' => $request['cart']
        ]);
        return redirect()->route('admin.orders.show', ['order' => $order->slug])->with([
            'status' => 'Данные корзины успешно изменены'
        ]);
    }

    public function order_comment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'comment' => 'nullable|string',
        ]);
        $order = Order::find($request->order_id);
        $data = $order->data;
        $data['comment'] = $request->comment;
        $order->update([
            'data' => $data
        ]);
        $order->addLog('Добавлен комментарий к заказу', $request->comment);
        return $request->comment;
    }

    public function checkStatusesSchedule($page = 1, $shipping_codes = ['cdek', 'cdek_courier', 'yandex'], $queue = true, $limit = 50)
    {
        $statuses = Status::query()->where('finish', true)->pluck('key')->toArray();
        $statuses = array_merge($statuses, ['is_processing', 'is_waiting', ' was_sended_to_store', 'is_assembled', 'cancelled', 'refund', 'test']);
        if (!$shipping_codes) {
            $shipping_codes = ['yandex'];
        }
        $orders = Order::whereIn('data_shipping->shipping-code', $shipping_codes)
            ->where('status', '!=', null)
            ->whereNotIn('status', $statuses)
            ->where(function ($query) {
                $query->where('data_shipping->cdek->uuid', '!=', null);
                $query->orWhere('data_shipping->pochta->barcode', '!=', null);
                $query->orWhere('data_shipping->cdek_courier->uuid', '!=', null);
                $query->orWhere('data_shipping->yandex->track', '!=', null);
                $query->orWhere('data_shipping->x5post->senderOrderId', '!=', null);
            })
            ->where(function ($query) {
                $query->where('status_updated_at', '<', now()->subHours(12));
                $query->orWhere('status', 'was_processed');
            })
            ->paginate($limit, ['*'], 'page', $page);
        $x5post_orders = collect([]);
        foreach ($orders as $order) {
            $data_shipping = $order->data_shipping;

            if ($data_shipping['shipping-code'] == 'yandex' && isset($data_shipping['yandex']['track']) && $order->status != 'refund') {
                (new BoxberryController())->checkStatus($order);
            } elseif (isset($data_shipping['cdek']['uuid']) || isset($data_shipping['cdek_courier']['uuid'])) {
                $old = Carbon::parse($order->created_at)->lt(Carbon::parse('2025-03-12 19:30'));
                (new CdekController($old))->checkStatus($order);
            } elseif ($data_shipping['shipping-code'] == 'pochta' && isset($data_shipping['pochta']['barcode'])) {
                (new PochtaController())->tracking($order);
            } elseif ($data_shipping['shipping-code'] == 'x5post' && isset($data_shipping['x5post']['orderId'])) {
                $x5post_orders[] = $order;
            }
        }
        if ($x5post_orders->count() > 0) {
            (new X5PostController())->checkStatuses($x5post_orders);
        }
        if ($orders->lastPage() > 1 && $page < $orders->lastPage() && $queue) {
            CheckOrdersStatusJob::dispatch(1, $shipping_codes)->onQueue('check_order_statuses');
        }
        return true;
    }

    public function checkStatus(Order $order)
    {
        $data_shipping = $order->data_shipping;

        if ($data_shipping['shipping-code'] == 'yandex' && isset($data_shipping['yandex']['track']) && $order->status != 'refund') {
            $status_result = (new BoxberryController())->checkStatus($order);
            if ($status_result) {
                return back()->with([
                    'status' => 'Статус заказа обновлен'
                ]);
            }
        } elseif (isset($data_shipping['cdek']['uuid']) || isset($data_shipping['cdek_courier']['uuid'])) {
            $old = Carbon::parse($order->created_at)->lt(Carbon::parse('2025-03-12 19:30'));
            $status_result = (new CdekController($old))->checkStatus($order);
            if ($status_result) {
                return back()->with([
                    'status' => 'Статус заказа обновлен'
                ]);
            }
        } elseif ($data_shipping['shipping-code'] == 'x5post' && isset($data_shipping['x5post']['orderId'])) {
            $status_result = (new X5PostController())->checkStatuses($order);
            if ($status_result) {
                return back()->with([
                    'status' => 'Статус заказа обновлен'
                ]);
            }
        }
        return back()->withErrors([
            'Невозможно получить статус по данному заказу'
        ]);
    }

    public function mailResend(Order $order)
    {
        $user = $order->user;
        if ($order->data['is_voucher'] ?? null) {
            (new MailSender($order->data['form']['email']))->confirmVouchersOrder($order, $user);
        } else {
            (new MailSender($order->data['form']['email']))->confirmOrder($order, $user);
        }

        return redirect()->route('admin.orders.show', ['order' => $order->slug])->with([
            'status' => 'Письмо о подверждении заказа повторно отправлено'
        ]);
    }

    public function copy(Order $order)
    {
        $data = $order->data;
        if (isset($data['copied'])) {
            return back()->withErrors([
                'Этот заказ уже был скопирован, новый id' . $data['copied']
            ]);
        }
        $data_cart = $order->data_cart;
        $data_shipping = $order->data_shipping;
        $new_data = $data;
        $new_data_cart = $data_cart;
        $new_data_shipping = $data_shipping;
        unset(
            $new_data['this_status'],
            $new_data['statuses'],
            $new_data['source'],
            $new_data_shipping['old_data'],
            $new_data_shipping['ticket'],
            $new_data_shipping['cdek'],
            $new_data_shipping['pochta'],
            $new_data_shipping['cdek_courier'],
            $new_data_shipping['yandex'],
            $new_data_shipping['x5post']
        );
        $new_data['double'] = $order->id;
        $new_data['copied_by'] = auth()->id();
        $slug = getCode(4);

        $new_order = Order::create([
            'user_id' => $order->user_id,
            'data' => $new_data,
            'data_cart' => $new_data_cart,
            'data_shipping' => $new_data_shipping,
            'amount' => $order->amount,
            'confirm' => $order->confirm,
            'slug' => $slug
        ]);
        $new_order->update([
            'slug' => $order->getOrderNumber() . '_' . $order->slug
        ]);
        // обновить корзину
        foreach ($new_data_cart as $cart_item) {
            $orderItem = OrderItem::setParams($order->id, $cart_item);
        }
        $data['copied'] = $new_order->id;
        $data['copied_by'] = auth()->id();
        $order->update([
            'data' => $data
        ]);
        $order->addLog('Создана копия заказа (id' . $new_order->id . ')');
        return redirect()->route('admin.orders.index')->with([
            'status' => 'Зкаказ был успешно скопирован. Новый id: ' . $new_order->getOrderNumber()
        ]);
    }

    public function requestTickets()
    {
        $bx_order_ids = Order::query()
            ->doesntHave('tickets')
            ->where(function ($query) {
                $query->whereIn('status', ['yandex_Заказ включен в акт. В обработке', 'yandex_загружен реестр им', 'yandex_заказ создан в личном кабинете', 'payment', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled']);
                $query->orWhere('status', null);
            })
            ->where('confirm', 1)
            ->where('data_shipping->ticket', null)
            ->whereIn('data_shipping->shipping-code', ['yandex'])
            ->where(function ($query) {
                $query->where('data_shipping->yandex->track', '!=', null);
            })->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
            ->orderBy('created_at', 'desc')
            ->pluck('id')->toArray();
        $yandex_chunks = array_chunk($bx_order_ids, 50);
        foreach ($yandex_chunks as $pack_ids) {
            getBoxberryTicketsJob::dispatch($pack_ids, auth()->id())->onQueue('boxberry_tickets');
        }

        $x5post_order_ids = Order::query()
            ->doesntHave('tickets')
            ->where(function ($query) {
                $query->whereIn('status', ['payment', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled', 'x5post_approved']);
                $query->orWhere('status', null);
            })
            ->where('confirm', 1)
            ->where('data_shipping->ticket', null)
            ->whereIn('data_shipping->shipping-code', ['x5post'])
            ->where(function ($query) {
                $query->where('data_shipping->x5post->created', '!=', null);
            })->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
            ->orderBy('created_at', 'desc')
            ->pluck('id')->toArray();

        $x5post_chunks = array_chunk($x5post_order_ids, 50);
        foreach ($x5post_chunks as $pack_ids) {
            getX5PostTicketsJob::dispatch($pack_ids, auth()->id())->onQueue('x5post_tickets');
        }

        $cdek_order_ids = Order::query()
            ->where(function ($query) {
                $query->where('data_shipping->cdek->uuid', '!=', null);
                $query->orWhere('data_shipping->cdek_courier->uuid', '!=', null);
            })
            ->doesntHave('tickets')
            ->where(function ($query) {
                $query->whereIn('status', ['cdek_1', 'cdek_created', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled', 'cdek_accepted']);
                $query->orWhere('status', null);
            })
            ->where('data_shipping->ticket', null)
            ->whereIn('data_shipping->shipping-code', ['cdek', 'cdek_courier'])
            ->where(function ($query) {
                $query->where('data_shipping->cdek->invoice_number', '!=', null);
                $query->orWhere('data_shipping->cdek_courier->invoice_number', '!=', null);
            })
            ->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->pluck('id')->toArray();
        $cdek_chunks = array_chunk($cdek_order_ids, 50);
        foreach ($cdek_chunks as $pack_ids) {
            getCdekTicketsJob::dispatch($pack_ids, auth()->id())->onQueue('cdek_tickets');
        }

        return back()->with([
            'status' => 'Запрос для создания этикеток успешно создан'
        ]);
    }

    public function batchUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => ['required', 'array'],
            'action' => ['required', 'string'],
        ]);
        $order_ids = $request->order_ids;
        $action = explode('|', $request->action);

        $orders = Order::select('id', 'user_id', 'data', 'data_cart', 'data_shipping', 'data_payment', 'amount')->whereIn('id', $order_ids);

        $message_success = '';
        if ($action[0] == 'set_status') { // устанавливаем статус
            $orders = $orders->get();
            $status = $action[1];
            foreach ($orders as $order) {
                $order->setStatus($status);
                if ($status == 'is_ready') {
                    $data = $order->data;
                    $data_shopping = $order->data_shipping;
                    $key = searchForId('is_ready', 'status', $data['statuses'] ?? []);
                    $pickup = Pickup::select()->where('code', $data_shopping['shipping-code'])->first();

                    if ($key === null && $pickup) {
                        (new Sender())->sendSms($order->user->phone, "Дорогой покупатель\nЗаказ " . $order->getOrderNumber() . " готов к выдаче\nАдрес и подробная информация о заказе в личном кабинете\n" . config('app.name'));
                        foreach ($order->user->tgChats as $tgChat) {
                            (new TelegramSender($tgChat))->customMessage(
                                "Дорогой покупатель\n\nЗаказ " . $order->getOrderNumber() . " готов к выдаче ✔️\n\n📍 Адрес пункта самовывоза - пр.Жукова 100Б\n(Вход магазин «Магнит»)\n\n⏰ Режим работы самовывоза:\nпн-пт с 11:00 до 20:00\nсб-вск с 9:00 до 18:00\n\n_\nLe Mousse – с заботой о твоей коже."
                            );
                        }
                    }
                } elseif ($status == 'is_assembled' && $order->data_shipping['shipping-code'] == 'pickup') {
                    $message = 'Здравствуйте!<br/><br/>';
                    $message .= 'Статус вашего заказа ' . config('app.name') . ' #' . $order->getOrderNumber() . ' был изменен на "Собран на складе".<br/>';
                    $message .= 'Вы можете заказать курьерскую доставку, написав нам <a href="https://wa.me/message/J6HM6AOKFBDGI1">https://wa.me/message/J6HM6AOKFBDGI1</a><br/><br/>';
                    $tg = Setting::where('key', 'tg_support')->first()->value;
                    $message .= 'Если у вас остались вопросы обратитесь пожалуйста к нам <a href="https://' . $tg . '">' . $tg . '</a>';
                    (new MailSender($order->data['form']['email']))->customMessage('Статус вашего заказа #' . $order->getOrderNumber() . ' изменен', $message);
                }
                // ставим статус
            }
            $message_success = 'Статус успешно изменен';
        } elseif ($action[0] == 'delivery') {
            if ($action[1] == 'send_to_cdek') { // отправка в cdek
                $orders->where(function ($query) {
                    $query->whereIn('status', ['is_processing', 'is_waiting', 'was_sended_to_store'])
                        ->orWhere('status', null);
                    return $query;
                });
                $orders_cdek = $orders->whereIn('data_shipping->shipping-code', ['cdek', 'cdek_courier'])
                    ->where(function ($query) {
                        $query->where('data_shipping->cdek->uuid', null);
                        $query->where('data_shipping->cdek_courier->uuid', null);
                    })
                    ->whereIn('id', $order_ids)
                    ->get();
                if ($orders_cdek->count()) {
                    SendOrdersToCdekJob::dispatch($orders_cdek->pluck('id')->toArray(), auth()->id())->onQueue('send_to_sdek');
                    ShippingLog::create([
                        'code' => 'cdek',
                        'title' => denum($orders_cdek->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь',
                        'text' => 'Заказы ' . implode(', ', $orders_cdek->pluck('id')->toArray()),
                    ]);
                    foreach ($orders_cdek as $order) {
                        $order->setStatus('was_processed');
                    }
                    return back()->with([
                        'status' => denum($orders_cdek->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                } else {
                    return back()->withErrors([
                        denum($orders_cdek->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                }
            } elseif ($action[1] == 'send_to_boxberry') { // отправка в cdek
                $orders->where(function ($query) {
                    $query->whereIn('status', ['is_processing', 'is_waiting', 'was_sended_to_store'])
                        ->orWhere('status', null);
                    return $query;
                });
                $orders_yandex = $orders->whereIn('data_shipping->shipping-code', ['yandex'])
                    ->where(function ($query) {
                        $query->where('data_shipping->yandex', null);
                    })
                    ->whereIn('id', $order_ids)
                    ->get();

                if ($orders_yandex->count()) {
                    SendOrdersToBoxberryJob::dispatch($orders_yandex->pluck('id')->toArray(), auth()->id())->onQueue('send_to_boxberry');
                    ShippingLog::create([
                        'code' => 'yandex',
                        'title' => denum($orders_yandex->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь',
                        'text' => 'Заказы ' . implode(', ', $orders_yandex->pluck('id')->toArray()),
                    ]);
                    foreach ($orders_yandex as $order) {
                        $order->setStatus('was_processed');
                    }
                    return back()->with([
                        'status' => denum($orders_yandex->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                } else {
                    return back()->withErrors([
                        denum($orders_yandex->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                }
            } elseif ($action[1] == 'send_to_pochta') { // отправка в почту
                $orders->where(function ($query) {
                    $query->whereIn('status', ['is_processing', 'is_waiting', 'was_sended_to_store'])
                        ->orWhere('status', null);
                    return $query;
                });
                $orders_pochta = $orders
                    ->where('data_shipping->pochta', null)
                    ->where('data_shipping->shipping-code', 'pochta')
                    ->whereIn('id', $order_ids)
                    ->get();

                if ($orders_pochta->count()) {
                    SendOrdersToPochtaJob::dispatch($orders_pochta->pluck('id')->toArray(), auth()->id())->onQueue('send_to_pochta');
                    ShippingLog::create([
                        'code' => 'pochta',
                        'title' => denum($orders_pochta->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь',
                        'text' => 'Заказы ' . implode(', ', $orders_pochta->pluck('id')->toArray()),
                    ]);
                    foreach ($orders_pochta as $order) {
                        $order->setStatus('was_processed');
                    }
                    return back()->with([
                        'status' => denum($orders_pochta->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                } else {
                    return back()->withErrors([
                        denum($orders_pochta->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                }
            } elseif ($action[1] == 'send_to_x5post') { // отправка в 5пост
                $orders->where(function ($query) {
                    $query->whereIn('status', ['is_processing', 'is_waiting', 'was_sended_to_store', 'test'])
                        ->orWhere('status', null);
                    return $query;
                });
                $orders_x5post = $orders->whereIn('data_shipping->shipping-code', ['x5post'])
                    ->where(function ($query) {
                        $query->where('data_shipping->x5post', null);
                    })
                    ->whereIn('id', $order_ids)
                    ->get();
                if ($orders_x5post->count()) {
                    SendOrdersToX5PostJob::dispatch($orders_x5post->pluck('id')->toArray(), auth()->id())->onQueue('send_to_x5post');
                    ShippingLog::create([
                        'code' => 'x5post',
                        'title' => denum($orders_x5post->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь',
                        'text' => 'Заказы ' . implode(', ', $orders_x5post->pluck('id')->toArray()),
                    ]);
                    foreach ($orders_x5post as $order) {
                        $order->setStatus('was_processed');
                    }
                    return back()->with([
                        'status' => denum($orders_x5post->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                } else {
                    return back()->withErrors([
                        denum($orders_x5post->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' поставлено в очередь'
                    ]);
                }
            }
        } elseif ($action[0] == 'check_status') {
            $orders = $orders->get();
            $x5post_orders = collect([]);
            foreach ($orders as $order) {
                $data_shipping = $order->data_shipping;

                if ($data_shipping['shipping-code'] == 'yandex' && isset($data_shipping['yandex']['track']) && $order->status != 'refund') {
                    (new BoxberryController())->checkStatus($order);
                } elseif (isset($data_shipping['cdek']['uuid']) || isset($data_shipping['cdek_courier']['uuid'])) {
                    $old = Carbon::parse($order->created_at)->lt(Carbon::parse('2025-03-12 19:30'));
                    (new CdekController($old))->checkStatus($order);
                } elseif ($data_shipping['shipping-code'] == 'x5post' && isset($data_shipping['x5post']['orderId'])) {
                    $x5post_orders[] = $order;
                }
            }
            if ($x5post_orders->count() > 0) {
                (new X5PostController())->checkStatuses($x5post_orders);
            }
            $message_success = 'Статус успешно изменен';
        }
        return back();
    }

    public function getStatisticForm(Request $request)
    {
        $seo = [
            'title' => 'Статистика заказов'
        ];
        return view('template.admin.orders.statistic', compact('seo'));
    }

    public function getStatistic(Request $request)
    {

        $pickups = Pickup::all();
        // $products = Product::dontCache()->orderBy('id', 'asc')->paginate(150);
        $orders = Order::select(
            'id',
            'data_cart',
            'data->total AS total',
            'data->discount AS discount',
            'data->promocode AS promocode',
            'data->voucher AS voucher',
            'data->bonuses AS bonuses',
            'data_shipping->price AS shipping_price',
            'data_shipping->shipping-code AS shipping_code',
            'amount'
        )->filtered(new SafeObject($request->toArray()))->withoutTest();
        $orders->where('status', '!=', 'cancelled');
        if (!(is_array($request->status) && in_array('refund', $request->status))) {
            $orders->where('status', '!=', 'refund');
        }
        if (auth()->user()->hasRole('admin')) {
            $orders->where('data->double', null);
        }
        $orders = $orders->paginate(5000);

        $orders_total = $orders->total();
        $stat = [];
        $total = 0;
        $total_shipping = ['total' => 0];
        $total_discount = 0;
        $total_discount_v = 0;
        $total_discount_p = 0;
        $total_discount_b = 0;
        $total_discount_v_sum = 0;
        $total_discount_p_sum = 0;
        $total_discount_b_sum = 0;
        $total_count = 0;
        foreach ($orders as $order) {
            if (!isset($total_shipping[$order->shipping_code])) {
                $total_shipping[$order->shipping_code] = 0;
            }
            $shipping_price = $order->shipping_price ?? 0;
            $total_shipping[$order->shipping_code] += round($shipping_price);
            $total_shipping['total'] += round($shipping_price);
            if (isset($order->discount) && $order->total + $shipping_price > $order->amount) {
                $discount = $order->total + $shipping_price - $order->amount;
                $discount = round($discount);
                $total_discount += $discount;
                if ($order->voucher !== null) {
                    $total_discount_v++;
                    $total_discount_v_sum += $discount;
                } elseif ($order->promocode !== null) {
                    $total_discount_p++;
                    $total_discount_p_sum += $discount;
                } elseif ($order->bonuses !== null) {
                    $total_discount_b++;
                    $total_discount_b_sum += $discount;
                }
            }
            $new_cart = $order->items;
            foreach ($new_cart as $item) {
                if (auth()->user()->hasRole('admin') && (strpos($item->name, 'подарок') !== false || strpos(mb_strtolower($item->name), 'миниверсия') !== false)) {
                    continue;
                }
                //        if($item->product_id == 1185){
                //          continue;
                //        }
                if (auth()->id() == 1 && $item->model == 'moistcrm' && $item->price == 0) {
                    Log::debug($order->id);
                }
                $product = Product::query()->with('product_sku')->where('id', $item->product_id)->first();
                $model = $product->product_sku?->name ?? $item->model;
                if ($item->parent_id) {
                    $item->name = 'Конструктор - ' . $item->name;
                }
                if ($item->price < 2) {
                    $model .= '-';
                }
                if (!isset($stat[$model])) {
                    $stat[$model] = [
                        'count' => 0,
                        'total' => 0
                    ];
                }
                $total_count += $item->qty;
                $stat[$model]['id'] = $item->product_id;
                $stat[$model]['name'] = $item->name;
                $stat[$model]['count'] += $item->qty;
                $stat[$model]['total'] += $item->qty * $item->price;
                $total += round($item->qty * $item->price);
            }
            //      $cart = $order->data_cart;
            //      foreach ($cart as $item) {
            //        if (auth()->user()->hasRole('admin') && (strpos($item['name'], 'подарок') !== false || strpos(mb_strtolower($item['name']), 'миниверсия') !== false)) {
            //          continue;
            //        }
            //        if (auth()->id()==1&&$item['model']=='sol-malina'){
            //          $item['name'] .= $order->getOrderNumber().', ';
            //        }
            //        if (!isset($stat[$item['model']])) {
            //          $stat[$item['model']] = [
            //              'count' => 0,
            //              'total' => 0
            //          ];
            //        }
            //        $total_count += $item['qty'];
            //        $stat[$item['model']]['id'] = $item['id'];
            //        $stat[$item['model']]['name'] = $item['name'];
            //        $stat[$item['model']]['count'] += $item['qty'];
            //        $stat[$item['model']]['total'] += $item['qty'] * $item['price'];
            //        $total += $item['qty'] * $item['price'];
            //      }
        }

        ksort($stat);
        return [
            'statistic' => $stat,
            'orders_total' => $orders_total,
            'total_count' => $total_count,
            'total_shipping' => $total_shipping,
            'total_discount' => $total_discount,
            'total_vouchers' => $total_discount_v,
            'total_vouchers_sum' => $total_discount_v_sum,
            'total_promocodes' => $total_discount_p,
            'total_promocodes_sum' => $total_discount_p_sum,
            'total_bonuses' => $total_discount_b,
            'total_bonuses_sum' => $total_discount_b_sum,
            'total' => $total - $total_discount,
            'this_page' => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
        ];
        //return view('admin.orders.statistic', compact('seo', 'stat', 'products', 'total_shipping', 'total_discount', 'total'));
    }

    public function export(Request $request)
    {
        ExportOrdersJob::dispatch($request->toArray(), 1, auth()->id())->onQueue('export_orders');
        return back()->with([
            'success' => 'Задача на экспорт заказов создана'
        ]);
    }

    public function export_job($request, $user_id): void
    {
        $export = new OrdersExport($request);
        $file_name = 'orders_' . now()->format('d-m-Y_H-i') . '.xlsx';
        $file_path = 'public/export/orders/' . $file_name;
        if (!file_exists(storage_path('app/public/export/orders'))) {
            mkdir(storage_path('app/public/export/orders'), 0777, true);
        }
        $count = Order::query()->select('id')->filtered(new SafeObject($request))->count();
        ExportFile::create([
            'name' => $file_name,
            'path' => $file_path,
            'type',
            'lines_count' => $count,
            'exported_by' => $user_id,
        ]);
        Excel::store($export, $file_path);
    }

}
