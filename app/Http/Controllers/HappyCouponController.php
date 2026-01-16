<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreCoupon;
use App\Models\User;
use App\Services\HappyCoupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class HappyCouponController extends Controller
{
    public function index(Order $order){
      $limit = $order->giftCoupons()->count();
      $attempts_left = $limit;
      if(Carbon::create($order->created_at)->lte(Carbon::create(2024, 5, 30))){
        abort(404);
      }
      if (!$limit) {
        abort(404, 'Данный заказ не участвует в акции');
      }
      if($order->giftCoupons()->where('data->promo20', true)->exists()){
        abort(403, 'Вам недоступна эта страница');
      }
      $prizes_grid = [
          [
//              asset('/img/happy_coupon/svitok_may.png'),
//              asset('/img/happy_coupon/svitok_may.png'),
//              asset('/img/happy_coupon/svitok_may.png'),
              asset('/img/happy_coupon/convert_dec.png'),
              asset('/img/happy_coupon/convert_dec.png'),
              asset('/img/happy_coupon/convert_dec.png'),
          ], //[],[]
          [
              asset('/img/happy_coupon/convert_dec.png'),
              asset('/img/happy_coupon/convert_dec.png'),
              asset('/img/happy_coupon/convert_dec.png'),
          ],
          [
              asset('/img/happy_coupon/convert_dec.png'),
              asset('/img/happy_coupon/convert_dec.png'),
              asset('/img/happy_coupon/convert_dec.png'),
          ],
      ];
      $animation = [
      ];
      for($i = 1;$i <= 99;$i++){
        $animation[] = asset('img/happy_coupon/animation-dec/img-'.($i).'.png?1');
      }
      $seo = [
          'title' => 'Счастливый купон'
      ];
      return view('template.cabinet.happy_coupon_dec', compact('seo', 'order', 'prizes_grid', 'attempts_left', 'animation'));
    }

  public function open(Order $order, Request $request){
    $coupone = $order->giftCoupons()->where('data->position->count', null)->inRandomOrder()->first();
    if (!$coupone){
      return response([
          'error' => 'all was already choosed'
      ]);
    }
    $count = $request->count;
    $coupone_data = [];
    if ($coupone->data){
      $coupone_data = $coupone->data;
    }
    $coupone_data['position']['count'] = $count;
    $coupone->update([
        'data' => $coupone_data
    ]);
    $prize = $coupone->prize;
    $limit = $order->giftCoupons()->count();
    $attempts_left = $limit - $order->giftCoupons()->where('data->position->count', '!=', null)->count();
    return response([
        'attempts_left' => $attempts_left,
        'prize' => [
          // 'id' => $prize->id,
            'image' => $prize->image ?? 2,
            'coupone' => $coupone->code,
            'name' => $prize->name,
            'position' => [
                'count' => $count
            ],
        ]
    ]);
  }

  public function opened(Order $order){
    $coupones = $order->giftCoupons()->where('data->position->count', '!=', null)->get();
    $prizes = [];
    if($coupones->count()){
      foreach($coupones as $coupone){
        $prize = $coupone->prize;
        $prizes[] = [
          // 'id' => $prize->id,
            'coupone' => $coupone->code,
            'image' => $prize->image ?? 1,
            'name' => $prize->name,
            'position' => [
                'count' => $coupone->data['position']['count'],
            ],
        ];
      }
    }
    $limit = $order->giftCoupons()->count();
    return response([
        'prizes' => $prizes,
        'limit' => $limit
    ]);
  }

  public function store(Request $request){
    if (!getSettings('happyCoupon')){
      abort(404);
    }
      $validate['store-coupon'] = ['required'];

      $errors = [];

      if(!auth()->check()){
        if($request->get('login')&&$request->get('login')==1){
          $request->validate($validate);
          $credentials = $request->validate([
              'email' => ['required', 'email'],
              'password' => ['required'],
          ]);
          if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
          }else{
            $errors[] = 'Указанные учетные данные не соответствуют нашим записям.';
          }
        }elseif($request->get('register')){
          $validate['first_name'] = ['required', 'string', 'max:30'];
          $validate['middle_name'] = ['nullable', 'string', 'max:30'];
          $validate['last_name'] = ['required', 'string', 'max:30'];
          $validate['email'] = ['required', 'string', 'email:rfc,dns', 'max:255', 'confirmed'];
          $validate['phone'] = ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'];
          $validate['password'] = ['required', 'confirmed'];
          $request->validate($validate);

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
          if (auth()->check()){
            $user = auth()->user();
          }else{
            $user = User::select('id', 'phone', 'options')->where(DB::raw('lower(email)'), '=', $email)->first();
          }
          if (!$user) {
            $user = User::where('phone', '=', $phone)->first();
            if ($user) {
              $errors[] = 'Данный телефон уже используется другим пользователем';
            }else{
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
            }
          } else {
            if ($user->phone != $phone) {
              $user_phone = User::where('phone', '=', $phone)->where(DB::raw('lower(email)'), '!=', $email)->first();
              if ($user_phone) {
                $errors[] = 'Данный телефон уже используется другим пользователем';
              }
            }else{
              Auth::loginUsingId($user->id);
            }
          }
          if (isset($user->options['blocked'])&&$user->options['blocked']){
            return back()->withInput()->withErrors([
                'message' => 'Что-то пошло не так.'
            ]);
          }
        }
      }
      if(!auth()->check()){
        return redirect()->route('happy_coupon.user', ['store-coupon' => request()->get('store-coupon')])->withErrors($errors);
      }
      $user = auth()->user();
      $code = mb_strtolower($request->get('store-coupon'));
      $storeCoupon = StoreCoupon::where(DB::raw('lower(code)'), '=', $code)->first();
      if (!$storeCoupon) {
        return back()->withErrors([
            'message' => 'Купон не найден'
        ]);
      }
      if ($storeCoupon->user_id && $storeCoupon->user_id != $user->id) {
        return back()->withErrors([
            'message' => 'Купон уже активирован'
        ]);
      }
      if($storeCoupon->order_id){
        return redirect()->route('happy_coupon', $storeCoupon->order->slug);
      }
      $exp_code = explode('-', $storeCoupon->code);
      $total = (int)substr($exp_code[0], 3);
      $data_order = [
          'total' => $total,
          'store_coupon' => true,
          'form' => [
              'email' => $user->email,
              'instagram' => null,
              'phone' => $user->phone,
              'full_name' => $user->name,
              'last_name' => $user->last_name ?? $user->name,
              'first_name' => $user->first_name ?? $user->name,
              'middle_name' => $user->middle_name,
              'oferta' => 1,
              'politika' => 1,
          ]
      ];
      $pickup = $storeCoupon->pickup;
      $data_shipping = [
          'shipping-code' => $pickup->code,
          'shipping-method' => $pickup->name,
          'country_code' => 0,
          'price' => 0
      ];
      $slug = getCode(4);
      $new_order = Order::create([
          'user_id' => $user->id,
          'data' => $data_order,
          'data_cart' => [],
          'data_shipping' => $data_shipping,
          'amount' => $total,
          'confirm' => 1,
          'slug' => $slug,
          'partner_id' => $partner ?? null
      ]);
      $new_order->update([
          'slug' => $new_order->getOrderNumber().'_'.$new_order->slug
      ]);
      (new HappyCoupon())->setPrizeToOrder($new_order);

      $storeCoupon->order_id = $new_order->id;
      $storeCoupon->user_id = $user->id;
      $storeCoupon->save();

      return redirect()->route('happy_coupon', $new_order->slug);
  }

  public function user(Request $request){
    if (!getSettings('happyCoupon')){
      abort(404);
    }
    $request->validate([
        'store-coupon' => ['required']
    ]);
    if(!auth()->check()){
      return view('template.cabinet.happy_coupon_user');
    }else{
      return redirect()->route('happy_coupon.store');
    }
  }
}
