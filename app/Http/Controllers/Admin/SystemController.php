<?php

namespace App\Http\Controllers\Admin;

use AntistressStore\CdekSDK2\Entity\Requests\Webhooks;
use App\Http\Controllers\Admin\Goods\ProductController;
use App\Http\Controllers\Admin\HappyCoupon\StoreCouponController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Shipping\BoxberryController;
use App\Http\Controllers\Shipping\CdekController;
use App\Http\Controllers\Shipping\PochtaController;
use App\Imports\ProductPriceImport;
use App\Imports\ProductSkuImport;
use App\Jobs\CheckOrdersStatusJob;
use App\Jobs\CompressContentImagesJob;
use App\Jobs\CustomJob;
use App\Jobs\RemoveEmailFromDashamailJob;
use App\Jobs\UpdateCdekPvzJob;
use App\Models\ActivityLog;
use App\Models\Bonus;
use App\Models\BonusTransaction;
use App\Models\BoxberryCity;
use App\Models\BoxberryPvz;
use App\Models\BoxberryRegion;
use App\Models\CdekCity;
use App\Models\CdekCourierCity;
use App\Models\CdekPvz;
use App\Models\CdekRegion;
use App\Models\City;
use App\Models\Content;
use App\Models\Country;
use App\Models\Coupon;
use App\Models\Coupone;
use App\Models\DashaMailList;
use App\Models\GiftCoupon;
use App\Models\MailingList;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pickup;
use App\Models\Prize;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\Region;
use App\Models\Status;
use App\Models\StoreCoupon;
use App\Models\TgChat;
use App\Models\TgMessage;
use App\Models\User;
use App\Models\Voucher;
use App\Models\Webhook;
use App\Models\X5PostCity;
use App\Models\X5PostPvz;
use App\Jobs\SetPrizeJob;
use App\Jobs\UpdateBoxberryPvzsJob;
use App\Notifications\TelegramNotification;
use App\Services\CompressModule;
use App\Services\DashamailService;
use App\Services\HappyCoupon;
use App\Services\MailSender;
use App\Services\PHPDasha;
use App\Services\Telegram\Client;
use App\Services\Telegram\Entities\Photo;
use App\Services\Telegram\Entities\Video;
use App\Services\TelegramSender;
use App\Services\TimeSlotService;
use App\Services\TreasureIsland;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemController extends Controller
{

  public function post(Request $request)
  {
    $uploadedImages = $request->session()->get('uploaded_images', []);
    dd($uploadedImages);
  }

  public function dashaAddMembers()
  {

    $DashaMailList = DashaMailList::query()->where('list_id', 273947)->first();
    if(!$DashaMailList){
      return false;
    }
    $users = User::query()
        ->doesntHave('dasha_mail_lists')
        ->where('is_subscribed_to_marketing', 1)
        ->doesntHave('roles')
        ->doesntHave('permissions')
        ->limit(100)
        ->get();
    $PHPDasha = new PHPDasha('27lookatme@gmail.com', 'tovhoz-zydho2-xoMnyg');
    $batch = [];
    foreach($users as $user){
      $user->dasha_mail_lists()->attach($DashaMailList->id);
      $batch[] = [
          'email' => $user->email
      ];
    }
    $res = $PHPDasha->lists_add_member_batch($DashaMailList->list_id, $batch);
    if($users->count()){
      CustomJob::dispatch()->onQueue('add_members_to_dasha');
    }
  }

  function removeRegionWord($string) {
    $words = ['город', 'область', 'регион', 'край', 'республика', 'автономный округ'];

    foreach ($words as $word) {
      if (str_ends_with($string, " " . $word)) {
        return rtrim(substr($string, 0, -strlen($word) - 1)); // -1 для пробела
      }
    }

    return $string;
  }
  function removeCityWord($string) {
    $words = [
        "аул",
        "г",
        "гп",
        "д",
        "дп",
        "ДПК",
        "ж/д_ст",
        "кп",
        "м",
        "массив",
        "нп",
        "обл",
        "п",
        "п/ст",
        "пгт",
        "р-н",
        "рп",
        "с",
        "с/с",
        "сл",
        "СНТ",
        "сп",
        "ст",
        "ст-ца",
        "тер",
        "тер.",
        "ТСН",
        "х"
    ];

    foreach ($words as $word) {
      if (str_ends_with($string, " " . $word)) {
        return rtrim(substr($string, 0, -strlen($word) - 1)); // -1 для пробела
      }
    }

    return $string;
  }

  function statistic($from, $to){
    $orders = Order::query()
        ->selectRaw('COUNT(*) as count, AVG(JSON_EXTRACT(data, "$.total")) as avg, SUM(JSON_EXTRACT(data, "$.total")) as sum, SUM(JSON_EXTRACT(data_shipping, "$.price")) as shipping, SUM(amount) as amount')
        ->where('confirm', 1)
        ->where('data->double', null)
        ->whereNotIn('status', ['cancelled', 'test', 'refund'])
        ->whereBetween('created_at', [$from, $to])
        ->first();
    $orders2 = Order::query()
        ->selectRaw('COUNT(*) as count')
        ->where('confirm', 1)
        ->where('data->double', null)
        ->whereIn('status', ['refund'])
        ->whereBetween('created_at', [$from, $to])
        ->first();
    $firstOrders = Order::query()
        ->select('user_id', DB::raw('MIN(created_at) as first_order_at'))
        ->whereNotIn('status', ['cancelled', 'test', 'refund'])
        ->where('confirm', true)
        ->where('data->double', null)
        ->groupBy('user_id');

    // Фильтруем тех, чей первый заказ пришелся на нужный период
    $newUsers = DB::table(DB::raw("({$firstOrders->toSql()}) as first_orders"))
        ->mergeBindings($firstOrders->getQuery()) // передаем bindings
        ->whereBetween('first_order_at', [$from, $to])
        ->selectRaw('COUNT(*) as count')
        ->first();

    $totalOrders = Order::whereBetween('created_at', [$from, $to])->count();

// Считаем количество уникальных пользователей, сделавших заказы в этом периоде
    $uniqueUsers = Order::query()
        ->where('confirm', 1)
        ->where('data->double', null)
        ->whereNotIn('status', ['cancelled', 'test', 'refund'])
        ->whereBetween('created_at', [$from, $to])
        ->distinct('user_id')
        ->count('user_id');

// Среднее количество заказов на пользователя
//    $averageOrdersPerUser = $uniqueUsers > 0 ? $totalOrders / $uniqueUsers : 0;

    $averageOrdersPerUser = DB::table('orders')
        ->selectRaw('AVG(order_count) as avg_orders')
        ->fromSub(function ($query) use ($from, $to) {
          $query->selectRaw('COUNT(*) as order_count')
              ->from('orders')
              ->where('confirm', 1)
              ->where('data->double', null)
              ->whereNotIn('status', ['cancelled', 'test', 'refund'])
              ->whereBetween('created_at', [$from, $to])
              ->groupBy('user_id');
        }, 'sub')
        ->value('avg_orders');

    echo 'Сумма выручки: '.formatPrice($orders->amount - $orders->shipping).' (без доставки, с учетом скидок)<br/>';
    echo 'Всего заказов: '.$orders->count.'<br/>';
    echo 'Средний чек: '.round(($orders->amount - $orders->shipping)/$orders->count).'<br/>';
    echo 'Возвраты: '.$orders2->count.' ('.($orders->count ? round($orders2->count/$orders->count*100, 2) : 0).'%)<br/>';
    echo 'Новые клиенты: '.$newUsers->count.' ('.($orders->count ? round($newUsers->count/$orders->count*100, 2) : 0).'%)<br/>';
    echo 'Повторные клиенты: '.$uniqueUsers-$newUsers->count.' ('.($uniqueUsers ? round(($uniqueUsers-$newUsers->count)/$uniqueUsers*100, 2) : 0).'%)<br/>';
    echo 'Среднее количество заказов на 1 пользователя: '.round($averageOrdersPerUser, 2).'<br/>';
  }
  public function test(){
    Status::flushQueryCache();
    dd(1);
    $order = Order::find(381496);
    (new BoxberryController())->prepareOrdersToBoxberry([381529,381547, 381553], 1);
    dd($order);
    $codes = [
        'ny20-virr',
'ny20-veaq',
'ny20-y8ln',
'ny20-ehfo',
'ny20-q1rh',
'ny20-holj',
'ny20-rfcy',
'ny20-ew54',
'ny20-1bkz',
'ny20-7pcb',
'ny20-dtzx',
'ny20-hazb',
'ny20-nbtz',
'ny20-0hjm',
'ny20-0qda',
'ny20-opoz',
'ny20-qnyh',
'ny20-044n',
'ny20-anff',
'ny20-c9dh',
'ny20-f0nt',
'ny20-rvfn',
'ny20-oamj',
'ny20-5nlm',
'ny20-hywk',
'ny20-fdnd',
'ny20-hldw',
'ny20-ygga',
'ny20-lnit',
'ny20-ykod',
'ny20-jqtf',
'ny20-qvma',
'ny20-pnr3',
'ny20-yf8a',
'ny20-eamu',
'ny20-3ob4',
'ny20-eyfy',
'ny20-pqwp',
'ny20-dsul',
'ny20-6puh',
'ny20-cig9',
'ny20-iww1',
'ny20-ta74',
'ny20-cfm1',
'ny20-3avf',
'ny20-1qpt',
'ny20-ijyr',
'ny20-rjnl',
'ny20-718l',
'ny20-0oya',
    ];
    foreach ($codes as $code) {
      $coupone = Coupone::query()->where('code', $code)->first();
      $coupone->update(['available_until' => '2026-03-03 23:59:00']);
    }
    die();
    for ($i = 0;$i<50;$i++){
      echo 'ny20-'.mb_strtolower(Str::random(4)).'<br/>';
    }
    die();
    echo 'LM ноябрь 2025<br/>';
    $this->statistic('2025-11-01 00:00:00', '2025-11-30 23:59:59');
    die();
    $bonuses = Bonus::query()->where('expired_at', '<', now())->where('amount', '>', 0)->get();
    foreach ($bonuses as $bonus) {
      $user = $bonus->user;
      $transaction = BonusTransaction::create([
          'bonus_id' => $bonus->id,
          'user_id' => $user->id,
          'amount' => $bonus->amount,
          'comment' => 'expired',
          'created_by' => null,
      ]);
      $bonus->update([
          'amount' => 0
      ]);
    }
    dd($bonuses);
    $bonuses = Bonus::query()->where('expired_at', '<', now())->get();
    dd($bonuses);

    dd();

    Excel::import(new ProductPriceImport(), public_path('products_17-11-2025_11-21.xlsx'));
    Product::flushQueryCache();
    die();
    $cdek_pvzs = CdekPvz::query()->where('city_code', 427)->pluck('code')->toArray();
    $boxberry_pvzs = BoxberryPvz::query()->whereIn('city_code', [194,7710951])->pluck('code')->toArray();
    $orders = Order::query()->where(function($query) use ($cdek_pvzs, $boxberry_pvzs) {
      $query->whereIn('data_shipping->cdek-pvz-id', $cdek_pvzs);
      $query->orWhereIn('data_shipping->cdek-boxberry-id', $boxberry_pvzs);
    })
        ->where('confirm', 1)
        //->limit(900)
        ->get();
    foreach($orders as $order){
      $user = $order->user;
      if(!$user->is_subscribed_to_marketing){
        continue;
      }
      echo $user->email.'<br/>';
    }
    die();
    $cdek_pvzs = CdekPvz::query()->where('city_code', 137)->pluck('code')->toArray();
    $boxberry_pvzs = BoxberryPvz::query()->where('city_code', 116)->pluck('code')->toArray();
    $orders = Order::query()->where(function($query) use ($cdek_pvzs, $boxberry_pvzs) {
      $query->whereIn('data_shipping->cdek-pvz-id', $cdek_pvzs);
      $query->orWhereIn('data_shipping->cdek-boxberry-id', $boxberry_pvzs);
    })
        ->where('confirm', 1)
        //->limit(900)
        ->get();
    foreach($orders as $order){
      $user = $order->user;
      if(!$user->is_subscribed_to_marketing){
        continue;
      }
      echo $user->email.'<br/>';
    }
    die();

    $message = 1;
    return response()->view('maintenanceBF1125end', compact('message'));
    die();
    $sku = ProductSku::query()->whereIn(DB::raw('lower(`name`)'), [
        mb_strtolower('LmT-0006'),
        mb_strtolower('mLmT-0002'),
        mb_strtolower('mLmT-0003'),
        mb_strtolower('LmT-0008'),
        mb_strtolower('LmM-0001'),
        mb_strtolower('LmM-0002'),
        mb_strtolower('LmM-0003'),
        mb_strtolower('LmM-0004'),
        mb_strtolower('LmM-0005'),
        mb_strtolower('LmM-0006'),
        mb_strtolower('LmM-0007'),
        mb_strtolower('LmM-0008'),
        mb_strtolower('LmM-0009'),
    ])->pluck('id')->toArray();
    $products = Product::query()->whereIn('product_sku_id', $sku)->get();
    foreach ($products as $product) {
      $options = $product->options;
      $options['tag50'] = '1';
      $product->update([
          'options' => $options,
      ]);
    }
    Product::flushQueryCache();
    dd($products);
    $orders = Order::query()->whereNull('payment_provider')->where('confirm', 1)->paginate(10000);
    foreach ($orders as $order) {
      $data_payment = $order->data_payment;
      if(isset($data_payment['InvId'])) {
        $payment_provider = 'robokassa';
      }elseif(isset($data_payment['InvoiceId'])){
        $payment_provider = 'cloudpayments';
      }elseif(isset($order->data['double'])){
        $double = Order::find($order->data['double']);
        if($double->payment_provider){
          $payment_provider = $double->payment_provider;
        }else{
          $data_payment = $order->data_payment;
          if(isset($data_payment['InvId'])) {
            $payment_provider = 'robokassa';
          }elseif(isset($data_payment['InvoiceId'])){
            $payment_provider = 'cloudpayments';
          }
        }
      }
      if(!isset($payment_provider)){
        dd($order);
      }
      $order->update([
          'payment_provider' => $payment_provider,
      ]);
    }
    dd($orders);
    $transactions = BonusTransaction::query()->where('created_by', null)->get();
    echo '<table>';
    foreach ($transactions as $transaction) {
      $target = \Carbon\Carbon::parse($transaction->created_at);
      echo '<tr>';
      echo '<td>'.$transaction->user->email.'</td>';
      echo '<td>'.$transaction->amount.'</td>';
      echo '<td>'.$transaction->comment.'</td>';
      echo '<td>'.$target->format('d.m.Y H:i').'</td>';
      echo '<td>'.$transaction->createdBy?->email.'</td>';
      echo '</tr>';
    }
    echo '</table>';
    die();
    $transactions = BonusTransaction::query()->where('created_by', '!=', null)->get();
    echo '<table>';
    foreach ($transactions as $transaction) {
      $target = \Carbon\Carbon::parse($transaction->created_at);
      echo '<tr>';
      echo '<td>'.$transaction->user->email.'</td>';
      echo '<td>'.$transaction->amount.'</td>';
      echo '<td>'.$transaction->comment.'</td>';
      echo '<td>'.$target->format('d.m.Y H:i').'</td>';
      echo '<td>'.$transaction->createdBy?->email.'</td>';
      echo '</tr>';
    }
    echo '</table>';
    die();
    function parseBonusOperation(string $text): int
    {
      // Находим число в скобках
      preg_match('/\((\d+)\)/', $text, $m);
      $amount = isset($m[1]) ? (int)$m[1] : 0;

      // Определяем тип операции
      $isWriteOff = mb_stripos($text, 'Списаны') !== false;

      // Если списание — делаем сумму отрицательной
      return $isWriteOff ? -$amount : $amount;
    }
    $activityLogs = ActivityLog::query()
        ->where('loggable_type', 'App\Models\User')
        ->where('action', 'like', '%бонусы%')
        ->get();
    foreach($activityLogs as $activityLog){
      $target = \Carbon\Carbon::parse($activityLog->created_at);

      $from = $target->copy()->subSecond(); // 14:35:00
      $to   = $target->copy()->addSecond();   // 14:35:59

      $summ = parseBonusOperation($activityLog->action);
      $transaction = BonusTransaction::query()
          ->where('amount', $summ)
          ->whereBetween('created_at', [$from, $to])
          ->where('user_id', $activityLog->loggable_id)
          ->first();
      if($transaction){
        $transaction->update([
            'created_by' => $activityLog->user_id,
        ]);
      }
    }
    dd($activityLogs);
    $roles = Role::whereHas('permissions', function ($query) {
      $query->where('name', 'Доступ к админпанели');
    })->pluck('name')->toArray();

    $users = User::query()
        ->whereHas('roles', function ($query) use ($roles) {
          $query->whereIn('name', $roles);
        })
        ->orWhereHas('permissions', function ($query) {
          $query->where('name', 'Доступ к админпанели');
        })
        ->whereNotIn('id', [333134, 1, 314738])
        ->get();
    $permissions = Permission::get();
    echo '<table>';
    foreach($users as $user){
      if(in_array($user->id,  [333134, 1, 314738])){
        continue;
      }
      $user->roles()->detach();
      $user->permissions()->detach();
      echo '<tr>';
      echo '<td>'.$user->name.'</td>';
      echo '<td>'.$user->phone.'</td>';
      echo '<td>'.$user->email.'</td>';
      echo '<td>';
      foreach($permissions as $permission){
        if($user->hasPermissionTo($permission->name)){
          echo $permission->name.', ';
        }
      }
      echo '</td>';
      echo '</tr>';
    }
    echo '</table>';
    die();
    $orders = Order::query()->where()->get();

    $bonuses = Bonus::query()->orderBy('amount', 'desc')->limit(30)->get();
    foreach($bonuses as $bonus){
      $user = $bonus->user;
      if($user->hasPermissionTo('Доступ к админпанели')){
        continue;
      }
      echo $user->email.' - '.formatPrice($bonus->amount).'<br/>';
    }
    die();
    $bonuses = Bonus::query()->sum('amount');
    $promo = Coupone::query()
        ->where('available_until', '>', now())
        ->where('count', '>', 0)
        ->where('type', 10)
        ->sum('amount');
    $promo_count = Coupone::query()
        ->where('available_until', '>', now())
        ->where('count', '>', 0)
        ->where('order_id', null)
        ->count();
    $vouchers = Voucher::query()
        ->where('available_until', '>', now())
        ->where('amount', '>', 0)
        ->where('order_id', null)
        ->sum('amount');
    echo 'Бонусы: '.formatPrice($bonuses).'<br/>';
    echo 'Промо: '.formatPrice($promo).'<br/>';
    echo 'Промо всего : '.$promo_count.'<br/>';
    echo 'Сертификаты: '.formatPrice($vouchers).'<br/>';
    die();

    echo 'LM октябрь 2025<br/>';
    $this->statistic('2025-10-01 00:00:00', '2025-10-31 23:59:59');
    die();
    $message = '';
    return response()->view('maintenanceAprFinish', compact('message'));
    $roles = Role::whereHas('permissions', function ($query) {
      $query->where('name', 'Доступ к админпанели');
    })->pluck('name')->toArray();

    $users = User::query()
        ->whereHas('roles', function ($query) use ($roles) {
          $query->whereIn('name', $roles);
        })
        ->orWhereHas('permissions', function ($query) {
          $query->where('name', 'Доступ к админпанели');
        })
        ->get();
    ;
    $permissions = Permission::get();
    echo '<table>';
    foreach($users as $user){
      echo '<tr>';
      echo '<td>'.$user->name.'</td>';
      echo '<td>'.$user->phone.'</td>';
      echo '<td>'.$user->email.'</td>';
      echo '<td>';
      foreach($permissions as $permission){
        if($user->hasPermissionTo($permission->name)){
          echo $permission->name.', ';
        }
      }
      echo '</td>';
      echo '</tr>';
    }
    echo '</table>';
    die();
    UpdateCdekPvzJob::dispatch(0)->onQueue('cdek_pvz');
    dd(1);
    echo 'LM сентябрь 2025<br/>';
    $this->statistic('2025-09-01 00:00:00', '2025-09-30 23:59:59');
    die();

//    dd($avgOrders);


    Excel::import(new ProductSkuImport(), public_path('sku-lm.xlsx'));
    die();
//    $users = User::query()->where('is_subscribed_to_marketing', false)->get();
//    foreach($users as $user){
//      RemoveEmailFromDashamailJob::dispatch($user->email);
//    }
//    dd($users);
    $dashamail = new DashamailService();

    $email = 'rusleeq@yandex.com';

    $result = $dashamail->deleteEmail($email);

    dd($result);
    return response()->json([
        'email' => $email,
        'found' => $status !== null,
        'status' => $status
    ]);

    $from = '2025-06-01 00:00:00';
    $to = '2025-07-31 23:59:59';

    echo 'LM январь 2025<br/>';
    $this->statistic('2025-01-01 00:00:00', '2025-01-31 23:59:59');
    echo '<br/>';
    echo 'LM февраль 2025<br/>';
    $this->statistic('2025-02-01 00:00:00', '2025-02-28 23:59:59');
    echo '<br/>';
    echo 'LM март 2025<br/>';
    $this->statistic('2025-03-01 00:00:00', '2025-03-31 23:59:59');
    echo '<br/>';
    echo 'LM апрель 2025<br/>';
    $this->statistic('2025-04-01 00:00:00', '2025-04-30 23:59:59');
    echo '<br/>';
    echo 'LM май 2025<br/>';
    $this->statistic('2025-05-01 00:00:00', '2025-05-31 23:59:59');
    echo '<br/>';
    echo 'LM июнь 2025<br/>';
    $this->statistic('2025-06-01 00:00:00', '2025-06-30 23:59:59');
    echo '<br/>';
    echo 'LM июль 2025<br/>';
    $this->statistic('2025-07-01 00:00:00', '2025-07-31 23:59:59');

    die();
    $products = Product::query()->whereBetween('id', [1294, 1305])->get();
    foreach($products as $product){
      $product->update([
          'hidden' => true
      ]);
    }
    Product::flushQueryCache();
    dd($products);
    die();
    $products = Product::query()->where('style_page->subtitle', '!=', null)->get();
    foreach($products as $product){
      $style_page = $product->style_page;
      $style_page['subtitle-page'] = $style_page['subtitle'];
      $product->update([
          'style_page' => $style_page
      ]);
    }
    Product::flushQueryCache();
    dd($products);
    $products1 = [1019,1020,1024,1025,1026,1030,1031,1032,1054,1068,1069,1070,1072,1074,1077,1078,1125,1126,1127,1128,1129,1131,1132,1135,1136,1159,1160,1161,1162,1163,1164,1165,1166,1167,1168,1169,1170,1171,1172,1173,1174,1175,1176,1177,1178,1179,1181,1182,1202,1203,1205,1214,1215,1256,1257,1259,1260,1261,1265,1266,1267,1268,1269,1270,1271,1276,1278,1279,1280,1281,1286,1287,1291,1294,1295,1296,1297,1298,1299,1300,1301,1302,1303,1304,1305];
    $products2 = Product::query()
        ->whereIn('id', $products1)
        ->get();
    foreach($products2 as $product){
      $style_page = $product->style_page;
      $style_page['k_info'] = 0;
      $product->update([
          'style_page' => $style_page
      ]);
    }
    $products3 = Product::query()
        ->whereNotIn('id', $products1)
        ->get();
    foreach($products3 as $product){
      $style_page = $product->style_page;
      $style_page['k_info'] = 1;
      $product->update([
          'style_page' => $style_page
      ]);
    }
    Product::flushQueryCache();
    echo implode(',', $products1);
    die();


    $sixMonthsAgo = Carbon::now()->subMonths(6);

//    $users = User::query()->doesntHave('roles')->whereHas('orders', function ($query) use ($sixMonthsAgo) {
//      $query->where('created_at', '>=', $sixMonthsAgo);
//    }, '>', 2) // Пользователи с более чем 2 заказами за последние полгода
//    ->orWhereHas('orders', function ($query) use ($sixMonthsAgo) {
//      $query->where('created_at', '>=', $sixMonthsAgo)
//          ->where('data->is_voucher', null)
//          ->where('amount', '>', 2000);
//    }, '=', 1)
//        ->inRandomOrder()
//        ->limit(100)
//        ->get();
    $users = User::query()->doesntHave('roles')->whereHas('orders', function ($query) {
      $query->where('confirm', 1)
          ->where('created_at', '>=', Carbon::now()->subMonths(6));
    }, '>=', 3)
        ->get();
    echo '<table>';

    echo '<tr>';
    echo '<td>ID</td>';
    echo '<td>Имя</td>';
    echo '<td>Email</td>';
    echo '<td>Телефон</td>';
    echo '<td>Заказов</td>';
    echo '<td>На сумму</td>';
    echo '<td>Дата последнего заказа</td>';
    echo '</tr>';
    foreach($users as $user){
      echo '<tr>';
      echo '<td>'.$user->id.'</td>';
      echo '<td>'.$user->name.'</td>';
      echo '<td>'.$user->email.'</td>';
      echo '<td>'.$user->phone.'</td>';
      echo '<td>'.$user->orders()->where('created_at', '>=', $sixMonthsAgo)->count().'</td>';
      echo '<td>'.$user->orders()->where('created_at', '>=', $sixMonthsAgo)->where('data->is_voucher', null)->sum('amount').'</td>';
      echo '<td>'.$user->orders()->orderByDesc('created_at')->first()->created_at->format('d.m.Y H:i').'</td>';
      echo '</tr>';
    }
    echo '</table>';
    die();
    $cdek_pvzs = CdekPvz::query()->where('city_code', 252)->pluck('code')->toArray();
    $boxberry_pvzs = BoxberryPvz::query()->where('city_code', 67)->pluck('code')->toArray();
    $orders = Order::query()->where(function($query) use ($cdek_pvzs, $boxberry_pvzs) {
      $query->whereIn('data_shipping->cdek-pvz-id', $cdek_pvzs);
      $query->orWhereIn('data_shipping->cdek-boxberry-id', $boxberry_pvzs);
    })
        ->where('confirm', 1)
          ->where('created_at', '>', '2024-04-17')
        //->limit(900)
        ->get();
    foreach($orders as $order){
      $user = $order->user;
      if(!$user->is_subscribed_to_marketing){
        continue;
      }
      echo $user->email.'<br/>';
    }
    die();

    $sixMonthsAgo = Carbon::now()->subMonths(6);

    $users = User::select('users.*')
        ->addSelect([
            'order_count' => DB::table('orders')
                ->whereColumn('users.id', 'orders.user_id')
                ->where('confirm', true)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->selectRaw('COUNT(*)')
        ])
        ->addSelect([
            'high_amount_order' => DB::table('orders')
                ->whereColumn('users.id', 'orders.user_id')
                ->where('confirm', true)
                ->where('created_at', '>=', $sixMonthsAgo)
                ->where('amount', '>', 2000)
                ->selectRaw('COUNT(*)')
        ])
        ->havingRaw('order_count > 2 OR (order_count = 1 AND high_amount_order = 1)')
        ->limit(10)
        ->get();
    dd($users);
    $tgClient = new Client();

    $tgChat = TgChat::find(1);
//    $tgMessage = TgMessage::create([
//        'tg_chat_id' => $tgChat->id,
//        'user_id' => $tgChat->user_id,
//        'text' => "test",
//        'time' => now()->format('Y-m-d H:i:s'),
//        'outgoing_message' => true
//    ]);
    $user = $tgChat->user;
    if(!$user->is_subscribed_to_marketing){
      Log::debug("user_id ".$user->id.": Выполнение метода userPhotoMessage остановлено: пользователь не подписан на маркетинговые рассылки.");
      return false;
    }
    $message = new Video();
//    $message->setVideo(asset('telegram/video/IMG_7321.MOV'));
    $message->setAttach(false);
    $message->setVideo('BAACAgIAAxkDAAEILvloQJqTywZYZDdSxs501Js4a9f1GAACXG0AAgcbCUr3Fa6gWEcrAzYE');
    $message->setThumbnail('AAMCAgADGQMAAQgu-WhAmpPLBlhkN1LGznTUmzhr1_UYAAJcbQACBxsJSvcVrqBYRysDAQAHbQADNgQ');
    $message->setChatId($tgChat->tg_user_id);
    $message->setCaption("test");
//    if($parse_mode){
//      $message->setParseMode($parse_mode);
//    }
    $res = $tgClient->sendVideo($message);

    dd($res, asset('telegram/video/IMG_7321.MOV'));
    $cdek_pvzs = CdekPvz::query()->where('city_code', 44)->pluck('code')->toArray();
    $boxberry_pvzs = BoxberryPvz::query()->where('city_code', 68)->pluck('code')->toArray();
    $orders = Order::query()->where(function($query) use ($cdek_pvzs, $boxberry_pvzs) {
      $query->whereIn('data_shipping->cdek-pvz-id', $cdek_pvzs);
      $query->orWhereIn('data_shipping->cdek-boxberry-id', $boxberry_pvzs);
    })
        ->where('confirm', 1)
//          ->where('created_at', '>', '2024-04-17')
        //->limit(900)
        ->get();
    foreach($orders as $order){
      $user = $order->user;
      if(!$user->is_subscribed_to_marketing){
        continue;
      }
      echo $user->email.'<br/>';
    }
    die();
    CompressModule::compressProductImages(1047);
    die();
    $message = '';
    return response()->view('maintenanceAprFinish', compact('message'));
    $prize_schedule = [
        [
            'time' => ['14:25', '14:40'],
            'date' => '2025-04-26',
            'prizes' => [198]
        ],
    ];
    $service = new TimeSlotService();

    $result = [];
    foreach($prize_schedule as $schedule){
      $date = $schedule['date'];
      $this_time = $schedule['time'];
      $taken_time = [];
      $prizes = $schedule['prizes'];
      shuffle($prizes);
      foreach($prizes as $prize_id){
        $randomTime = $service->getRandomAvailableTime(
            $this_time,
            $taken_time
        );
        $taken_time[] = $randomTime;
        $prize = Prize::find($prize_id);
        $scheduleTime = Carbon::parse($date.' '.$randomTime);
        SetPrizeJob::dispatch($prize->id)->delay($scheduleTime)->onQueue('set_prize_'.translit($prize->name));
        $result[$scheduleTime->format('d.m.y H:i')] = $prize->name;
      }
    }
    dd(1);
    $orders = Order::query()
        ->where('data->total', '>=', 5999)
        ->where('confirm', 1)
        ->whereBetween('updated_at', ['2025-04-25 10:00', '2025-04-25 10:30'])
        ->count();
    dd($orders);
//    $gifts = Prize::query()
//        ->selectRaw("
//                id,
//                CASE
//                    WHEN id IN (" . implode(',', Prize::RED) . ") THEN count * 1.5
//                    ELSE count
//                END as count,
//                total
//            ")
//        ->where('count', '>', 0)
//        ->where('active', true)
//        ->get()->toArray();
//    dd($gifts);
    $prizes = Prize::query()
//        ->whereIn('id', Prize::GENERAL)
        ->where('active', true)
        ->get();
    foreach($prizes as $prize){
      $prize->update([
          'count' => $prize->total
      ]);
      echo $prize->id.',';
    }
    die();

    Status::create([
        'key' => 'not_in_demand',
        'name' => 'Не востребован'
    ]);
    Status::flushQueryCache();
    dd(1);
    $orders = Order::query()->whereIn('city_id', [
        request()->city,
//          101, // Тюмень
//          105, // Уфа
//          189, // Самара
//          113, // Новосибирск
    ])
        ->whereBetween('created_at', ['2024-04-17 00:00:00', '2025-04-17 23:59:59'])
        ->get();
    $emails = [];
    foreach($orders as $order){
      $mail = $order->user->email;
      if(!in_array($mail, $emails)){
        $emails[] = $mail;
      }
    }
    foreach($emails as $email){
      echo $email.'<br/>';
    }
    die();
    $orders = Order::query()
        ->select('data_shipping', 'id')
        ->whereIn('data_shipping->shipping-code', ['cdek', 'cdek_courier', 'boxberry', 'pickup'])
        ->whereNotNull('country_id')
        ->whereNotNull('region_id')
        ->whereNull('city_id')
        ->orderByDesc('id')
        ->paginate(10000);
    $i = 0;
    $n = 0;
    foreach($orders as $order){
      $country_id = null;
      $region_id = null;
      $city_id = null;
      if($order->data_shipping['shipping-code'] == 'boxberry'){
        $pvz = BoxberryPvz::query()->where('code', $order->data_shipping['boxberry-pvz-id'])->first();
        if($pvz){
          $boxberry_city = $pvz->city;
          if($boxberry_city){
            $country_id = $boxberry_city->lm_country_id;
            $region_id = $boxberry_city->lm_region_id;
            $city_id = $boxberry_city->lm_city_id;
          }
        }

      }elseif($order->data_shipping['shipping-code'] == 'cdek'){
        $pvz = CdekPvz::query()->where('code', $order->data_shipping['cdek-pvz-id'])->first();
        $cdek_city = $pvz->cdek_city;
        if($cdek_city){
          $country_id = $cdek_city->lm_country_id;
          $region_id = $cdek_city->lm_region_id;
          $city_id = $cdek_city->lm_city_id;
        }
      }elseif($order->data_shipping['shipping-code'] == 'cdek_courier'){
        $cdek_city = CdekCity::query()->where('code', $order->data_shipping['cdek_courier-form-city'])->first();
        if($cdek_city){
          $country_id = $cdek_city->lm_country_id;
          $region_id = $cdek_city->lm_region_id;
          $city_id = $cdek_city->lm_city_id;
        }
      }elseif($order->data_shipping['shipping-code'] == 'x5post'){
        $pvz = X5PostPvz::query()->where('mdmCode', $order->data_shipping['x5post-pvz-id'])->first();
        $x5post_city = $pvz->city;
        if($cdek_city){
          $country_id = $cdek_city->lm_country_id;
          $region_id = $cdek_city->lm_region_id;
          $city_id = $cdek_city->lm_city_id;
        }
      }elseif($order->data_shipping['shipping-code'] == 'pickup'){
        $country_id = 1;
        $region_id = 66;
        $city_id = 186;
      }
      if($city_id&&$region_id&&$country_id){
        $order->update([
            'country_id' => $country_id,
            'region_id' => $region_id,
            'city_id' => $city_id,
        ]);
        $i++;
      }else{
        $n++;
      }
    }
    dd($i, $n, $orders);
//    $users = User::query()->select('id', 'email', 'name', 'phone')->whereRaw('MONTH(birthday) = ?', [now()->addDays(3)->month])
//        ->whereRaw('DAY(birthday) = ?', [30])
//        ->whereDoesntHave('bonus_transactions', function(Builder $builder){
//          $builder->where('created_at', '>', now()->subDays(360)->format('Y-m-d H:i:s'));
//          $builder->where('comment', 'birthday');
//        })
//        ->get();
//    foreach($users as $user){
//      $user->addBonuses(500, 'birthday');
//      (new MailSender($user->email))->birthdayGreetings(500);
//      foreach($user->tgChats as $tgChat){
//        (new TelegramSender($tgChat))->birthdayGreetings(500);
//      }
//      echo $user->email.'<br/>';
//      echo $user->name.'<br/>';
//      echo $user->phone.'<br/>';
//      echo '<br/>';
//    }
//    dd($users);
    $cdek_webhook = 'https://lemousse.shop/api/webhook/cdek/status';
    $webhook = (new CdekController())->cdek->setWebhooks(
        (new Webhooks())
            ->setUrl($cdek_webhook)
            ->setType('ORDER_STATUS'));
    $webhook_uuid = $webhook->getEntityUuid();
    $create_webhook = Webhook::create([
        'url' => $cdek_webhook,
        'name' => 'ORDER_STATUS',
        'code' => 'cdek',
        'data' => json_encode([
            'uuid' => $webhook_uuid
        ]),
    ]);
    dd($webhook);

    $products = Product::query()->where('keywords', '!=', null)->get();
    foreach($products as $product){
      $keywords = $product->keywords;
      $name = $product->name;
      $product->cleanKeywords();
    }
    Product::flushQueryCache();
    dd(1);

    $products = Product::query()->where('keywords', '!=', null)->get();
    foreach($products as $product){
      $keywords = $product->keywords;
      $name = $product->name;
      $product->cleanKeywords();
    }
    Product::flushQueryCache();
    dd(1);
    return view('test');
    $orders = Order::query()->whereIn('city_id', [
        16, 49, 97, 101, 105, 113, 128, 186, 187, 189, 1575
    ])
        ->whereBetween('created_at', ['2024-09-01 00:00:00', '2024-11-30 23:59:59'])
        ->get();
    $emails = [];
    foreach($orders as $order){
      $mail = $order->user->email;
      if(!in_array($mail, $emails)){
        $emails[] = $mail;
      }
    }
    foreach($emails as $email){
      echo $email.'<br/>';
    }
    die();
//    $x5post_cities = X5PostCity::query()->get();
//    foreach($x5post_cities as $city){
//      $name = trim($this->removeCityWord($city->name));
//      $region = $city->region;
//      $db_city = City::query()
//          ->where('name', 'like', '%'.$name.'%')
//          ->where('region_id', $region->lm_region_id)
//          ->first();
//      if(!$db_city){
//        $db_city = City::create([
//            'name' => $name,
//            'country_id' => 1,
//            'region_id' => $region->lm_region_id,
//        ]);
//      }
//      $city->update([
//          'lm_country_id' => 1,
//          'lm_region_id' => $region->lm_region_id,
//          'lm_city_id' => $db_city->id
//      ]);
//    }
//    dd(1);
//    $x5post_cities = X5PostCity::query()
//        ->select('city_type')
//        ->groupBy('city_type')
//        ->get()
//        ->pluck('city_type')->toArray();
//    $x5post_regions = X5PostRegion::query()->where('lm_region_id', null)->get();
//    foreach($x5post_regions as $region){
//      $name = trim($this->removeRegionWord($region->name));
//      $db_region = Region::query()->where('name', 'like', '%'.$name.'%')->first();
//      if(!$db_region){
//        $db_region = Region::create([
//            'name' => $name,
//            'country_id' => 1,
//        ]);
//      }
//      $region->update([
//          'lm_country_id' => 1,
//          'lm_region_id' => $db_region->id
//      ]);
//    }
    $boxberry_regions = BoxberryRegion::get();
    foreach($boxberry_regions as $region){
      $db_region = Region::query()->where('name', 'like', '%'.$region->name.'%')->first();
      if(!$db_region){
        $db_region = Region::create([
            'name' => $region->name,
            'country_id' => 1,
        ]);
      }
      $region->update([
          'lm_country_id' => 1,
          'lm_region_id' => $db_region->id
      ]);
    }

    $cdek_regions = CdekRegion::get();
    foreach($cdek_regions as $region){
      $db_region = Region::query()->where('name', $region->region)->first();
      $country = Country::where('name', $region->country)->first();
      if(!$country){
        continue;
      }
      if(!$db_region){
        $db_region = Region::create([
            'name' => $region->region,
            'country_id' => $country->id,
        ]);
      }

      $region->update([
          'lm_country_id' => $country->id,
          'lm_region_id' => $db_region->id
      ]);
    }
//    dd($cdek_regions);
    $boxberry_cities = BoxberryCity::get();
    foreach($boxberry_cities as $city){
      $region = null;
      $boxberry_region = $city->region;
      if($boxberry_region){
        $region = $boxberry_region->lm_region;
      }
      if(!$region){
        continue;
      }
      $country = $region->country;
      if(!$country){
        continue;
      }
      $db_city = City::query()->where('name', $city->Name)->where('region_id', $region->id)->first();
      if(!$db_city){
        $db_city = City::create([
            'name' => $city->Name,
            'country_id' => $country->id,
            'region_id' => $region->id,
        ]);
      }

      $city->update([
          'lm_country_id' => $country->id,
          'lm_city_id' => $db_city->id,
          'lm_region_id' => $region->id,
      ]);
    }
    $cdek_cities = CdekCity::get();
    foreach($cdek_cities as $city){
      $region = null;
      $cdek_region = $city->cdek_region;
      if($cdek_region){
        $region = $cdek_region->lm_region;
      }
      if(!$region){
        continue;
      }
      $country = $region->country;
      if(!$country){
        continue;
      }
      $db_city = City::query()->where('name', $city->city)->where('region_id', $region->id)->first();
      if(!$db_city){
        $db_city = City::create([
            'name' => $city->city,
            'country_id' => $country->id,
            'region_id' => $region->id,
        ]);
      }

      $city->update([
          'country' => $country->name,
          'lm_country_id' => $country->id,
          'lm_city_id' => $db_city->id,
          'lm_region_id' => $region->id,
      ]);
    }
    $x5post_cities = X5PostCity::query()->get();
    foreach($x5post_cities as $city){
      $name = trim($this->removeCityWord($city->name));
      $region = $city->region;
      $db_city = City::query()
          ->where('name', 'like', '%'.$name.'%')
          ->where('region_id', $region->lm_region_id)
          ->first();
      if(!$db_city){
        $db_city = City::create([
            'name' => $name,
            'country_id' => 1,
            'region_id' => $region->lm_region_id,
        ]);
      }
      $city->update([
          'lm_country_id' => 1,
          'lm_region_id' => $region->lm_region_id,
          'lm_city_id' => $db_city->id
      ]);
    }
    dd(1);



    $products = Product::query()->with('category')->where('type_id', 1)->get();
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Имя</th>';
    echo '<th>Артикул</th>';
    echo '<th>Категория</th>';
    echo '<th>Вес г.</th>';
    echo '<th>Объем</th>';
    echo '<th>Цена</th>';
    echo '<th>Количество</th>';
    echo '<th>Статус наличия</th>';
    echo '<th>Скрытй товар</th>';
    echo '<th>Порядок</th>';
    echo '<th>Ключевые слова</th>';
    echo '<th>Ссылка</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($products as $product){
      echo '<tr>';
      echo '<td>'.$product->id.'</td>';
      echo '<td>'.$product->name.'</td>';
      echo '<td>'.$product->sku.'</td>';
      echo '<td>'.$product->category?->title.'</td>';
      echo '<td>'.$product->weight.'</td>';
      echo '<td>'.$product->volume.'</td>';
      echo '<td>'.$product->price.'</td>';
      echo '<td>'.$product->quantity.'</td>';
      echo '<td>'.($product->status ? 'Да' : 'Нет').'</td>';
      echo '<td>'.($product->hidden ? 'Да' : 'Нет').'</td>';
      echo '<td>'.$product->order.'</td>';
      echo '<td>'.$product->keywords.'</td>';
      echo '<td>'.route('product.index', $product->slug).'</td>';
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    die();
//    $message = getSettings('maintenanceNotification');
//    return response()->view('maintenance', compact('message'));
//    (new UserController())->surveyGifts();
    $this->telegramMailing();
    die();
    $orders = Order::query()->where('data->discount', '>', 0)->where('data->all_fields->discount', '>', 0)->toSql();
    dd($orders);
    $orders = Order::query()
        ->whereHas('items', function(Builder $builder){
            $builder->whereIn('product_id', [1158,1231,1245]);
          })
        ->whereHas('tickets', function(Builder $builder){
          $builder->where('tickets.created_at', '>', '2024-12-20 12:50');
        })
        ->whereIn('data_shipping->shipping-code', ['boxberry'])
        ->where('created_at', '>', '2024-12-18')
        ->get();
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Товар</th>';
    echo '<th>Дата этикетки</th>';
    echo '<th>Этикетка</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach($orders as $order){
      $ticket = $order->tickets()->first();
      $items = $order->items()->whereIn('product_id', [1158,1231,1245])->get();
//      echo '<a href="'.$order->data_shipping['ticket'].'">'.$order->data_shipping['ticket'].'</a> - '.Carbon::parse($ticket->created_at)->format('d.m.Y H:i').' - '.$order->id.'<br/>';
      echo '<tr>';
      echo '<td>'.$order->id.'</td>';
      echo '<td>';
      foreach($items as $item){
        echo ($item->product_id-1000).',';
      }
      echo '</td>';
      echo '<td>'.Carbon::parse($ticket->created_at)->format('d.m.Y H:i').'</td>';
      echo '<td><a href="'.$order->data_shipping['ticket'].'">'.$order->data_shipping['ticket'].'</a></td>';
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    die();
    dd($orders);
//    $this->prizesStat();
//    die();
//    $this->telegramMailing();
    die(123);

//      foreach($prize_schedule as $schedule){
//        $this_time = $schedule['time'];
//        $taken_time = [];
//        $scheduleTime = Carbon::parse('2024-12-05 19:30');
//        $prizes = shuffle($schedule['prizes']);
//        foreach($schedule['prizes'] as $prize_id){
//          $prize = Prize::find($prize_id);
//          SetPrizeJob::dispatch($prize->id)->delay($scheduleTime)->onQueue('set_prize_'.$prize->code);
//          $result[$scheduleTime->format('d.m.y H:i')] = $prize->name;
//          $scheduleTime->addMinutes(15);
//        }
//      }

    $sorted = sortArrayByDate($result);
    foreach($sorted as $time => $prize){
      echo $time.' - '.$prize.'<br/>';
    }
    die();
//    (new ProductController())->updateViewers();
//    $products = Product::query()
//        ->select('id', 'options')
//        ->where('quantity', '>', 0)
//        ->where('status', true)
//        ->where('type_id', 1)
//        ->count();
//    dd($products);
//    function removeWords($text) {
//      // Используем регулярное выражение для поиска целых слов
//      // \b - граница слова
//      $pattern = '/\b(для|от)\b/u';
//
//      // Заменяем найденные слова на пустую строку
//      $result = preg_replace($pattern, '', $text);
//
//      // Удаляем лишние пробелы, которые могли образоваться
//      $result = preg_replace('/\s+/', ' ', $result);
//
//      // Убираем пробелы в начале и конце строки
//      return trim($result);
//    }
//    $products = Product::query()->where('keywords', '!=', null)->get();
//    foreach($products as $product){
//      $keywords = mb_strtolower($product->keywords);
//      echo removeWords($keywords).'<br/>';
//      $product->update([
//          'keywords' => removeWords($keywords)
//      ]);
//    }
//    die();
    $order = Order::find(355194);
    (new HappyCoupon())->setPrizeToOrder($order);
    dd($order);
    $prizes = Prize::query()
        ->whereIn('id', Prize::GENERAL)
        ->where('active', true)
        ->get();
    foreach($prizes as $prize){
      $prize->update([
          'count' => $prize->total
      ]);
      echo $prize->id.',';
    }
    die();
    Content::flushQueryCache();
    die();
    CompressModule::compressContentImages(19);
    $content = Content::find(19);
    $images = $content->image_data['_request'] ?? null;
    foreach($content->image_data as $key => $value){
      if(isset($value['img'])&&!empty($value['img'])) {
        $compressedImages = CompressModule::compressImage($value['img'], [480,768,1200,1920], 2000);
        $images[$key]['size'] = $compressedImages;
      }
    }
    $content->update([
        'image_data' => $images
    ]);
    dd($images);
    die();
    CustomJob::dispatch()->delay(Carbon::parse('2024-12-01 00:00:00'));
    die();
    $prices = [

    ];
    foreach($prices as $id => $price){
      $product = Product::find($id);
      $product->update([
          'price' => $price
      ]);
    }
    Product::flushQueryCache();
    die();
    $products = Product::where('type_id', 1)->get();
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Имя</th>';
    echo '<th>Описание</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($products as $product){
      $text = ($product->style_page['age'] ?? '') ? $product->style_page['age'] : '';
      $text .= ($product->style_page['features'] ?? '') ? '<br/><br/>'.$product->style_page['features'] : '';
      $text .= ($product->style_page['subtitle'] ?? '') ? '<br/><br/>'.$product->style_page['subtitle'] : '';
      $text .= ($product->style_page['description'] ?? '') ? '<br/><br/>'.$product->style_page['description'] : '';
      $text .= ($product->style_page['cardsDescription'] ?? '') ? '<br/><br/>'.$product->style_page['cardsDescription'] : '';
      $text .= ($product->style_page['typeOfSkinItalic'] ?? '') ? '<br/><br/>'.$product->style_page['typeOfSkinItalic'] : '';
      $text .= ($product->style_page['care_compatibility'] ?? '') ? '<br/><br/>'.$product->style_page['care_compatibility'] : '';
      $text .= ($product->style_page['activeComponentsText'] ?? '') ? '<br/><br/>'.$product->style_page['activeComponentsText'] : '';

      echo '<tr>';
      echo '<td>'.$product->id.'</td>';
      echo '<td>'.$product->name.'</td>';
      echo '<td>'.$text.'</td>';
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    die();
    dd($_SERVER['REMOTE_ADDR']);

    dd(1);

    CustomJob::dispatch()->onQueue('add_members_to_dasha');
    dd(1);
    $DashaMailList = DashaMailList::query()->where('list_id', 273947)->first();
    if(!$DashaMailList){
      return false;
    }
    $users = User::query()
        ->doesntHave('dasha_mail_lists')
        ->where('is_subscribed_to_marketing', 1)
        ->doesntHave('permissions')
        ->limit(10)
        ->get();
    $batch = [];

//    $DashaMailList = DashaMailList::create([
//        'list_id' => 273947,
//        'name' => 'Le mousse',
//    ]);
//    dd(1);
    $PHPDasha = new PHPDasha('27lookatme@gmail.com', 'tovhoz-zydho2-xoMnyg');
//    $res = $PHPDasha->lists_add('Le mousse', [
//        'company' => 'ИП Нечаева Ольга Андреевна',
//        'country' => 'Россия',
//        'url' => 'https://lemousse.shop/',
//        'phone' => '+7 (8442) 51-50-05',
//    ]);
    $batch = [];
    foreach($users as $user){
      $user->dasha_mail_lists()->attach($DashaMailList->id);
      $batch[] = [
          'email' => $user->email
      ];
    }
    $res = $PHPDasha->lists_add_member_batch($DashaMailList->list_id, $batch);
    dd($res);
    $users = User::query()
        ->where('is_subscribed_to_marketing', 1)
        ->doesntHave('permissions')
        ->paginate(1000);
    foreach($users as $user){
      $PHPDasha->lists_add_member(273947, $user->email);
    }
    dd($users);
    $path = 'app/public/images/'.now()->format('Y-m-d').'_dyson/';
    if (!file_exists(storage_path($path))) {
      mkdir(storage_path($path), 0777, true);
    }
    $result = [];
    $i = 0;
    while ($i < 10) {
      $code = getCode(3).' - '.getCode(3).' - '.getCode(3);

      $img = Image::make(public_path('img/dyson.png'));
      $width = $img->width();
      $img->text(mb_strtoupper($code), $width/2, 955, function ($font) {
        $font->file(public_path('img/vch/CormorantInfant-Regular.ttf'));
        $font->size(50);
        $font->color('#000');
        $font->align('center');
      });


      $img_link = storage_path($path . $code . '.png');
      $img->save($img_link);
      // вывод на экран
//      $finfo = finfo_open(FILEINFO_MIME_TYPE);
//      $mimeType = finfo_file($finfo, $img_link);
//      finfo_close($finfo);
//
//      // Устанавливаем заголовки
//      header("Content-Type: $mimeType");
//      header("Content-Length: " . filesize($img_link));
//
//      // Отключаем буферизацию вывода
//      if (ob_get_level()) {
//        ob_end_clean();
//      }
//
//      // Выводим содержимое файла
//      readfile($img_link);
//      die();
      $result[] = [$code, storageToAsset($img_link)];
      $i++;
    }
    foreach($result as $res){
      echo $res[0].'<br/>';
    }
    dd($result);
    // база 273947

//    $memcached = new \Memcached();
//    $stats = $memcached->getStats();
//    dd($stats);
//    $memcached = Cache::getMemcached();
//    $key = 'le_mousse_cache_:825ef7601f5140b8d31d4993ace0e822585d81ce:leqc:0c891c6fc1fd9376c2e19dbfd85ef3b03ce8c6adfd292d75a0e95bb23a52ab9a';
//    $cas = null;
//    $result = $memcached->get($key, null, \Memcached::GET_EXTENDED);
//    dd($result);
    dd(config('cache.default'),
        getSettings('payment_test'),
        \App\Models\Setting::query()->where('key', 'payment_test')->first(),
        \App\Models\Setting::query()->where('key', 'payment_test')->first()->getCacheKey());
//    $users = User::query()->paginate(50000);
//    foreach($users as $user){
//      $user->update([
//          'is_subscribed_to_marketing' => true
//      ]);
//    }
    //
//    dd($users);
//    function trimToParentheses($string) {
//      $pos = strpos($string, '(');
//      if ($pos !== false) {
//        $string = substr($string, 0, $pos);
//      }
//      return trim($string);
//    }
//    $order_ids = [345968, 345958, 345841, 345651, 345198, 345093];
//    $orders = Order::whereIn('id', $order_ids)->get();
//    foreach($orders as $order){
//      $data_shipping = $order->data_shipping;
//      $region = $data_shipping['cdek_courier-form-region'];
//      $city = trimToParentheses($data_shipping['cdek_courier-form-city']);
//      $courier_city = CdekCourierCity::where('region', $region)->where('city', $city)->first();
//      if($courier_city){
//        $data_shipping['cdek_courier-form-city'] = $courier_city->code;
//        $order->update([
//            'data_shipping' => $data_shipping
//        ]);
//      }
//
//    }
//    dd(1);

//    Product::flushQueryCache();
//    dd(1);
//    $product = Product::find(1085);
//    $refill_img = null;
//    if($product->refill && isset($product->refill->style_page['cardImage']['image'])){
//      $r_img = $product->refill->style_page['cardImage']['image'];
//      $refill_img = generatePictureHtmlCached($r_img, $product->refill->name.'111');
//    }
//    dd($refill_img, $r_img);

//    $vouchers = (new VoucherController)->create_voucher('1000n', 1);
//    dd($vouchers);

    $city = City::query()->where('name', 'Санкт-Петербург')->first();
    $city_id = $city->id;
    $users = User::query()->whereHas('orders', function (Builder $builder) use ($city_id) {
      $builder
            ->where('orders.city_id', $city_id)
//          ->where(function ($query){
//            $query->where('orders.data_shipping', 'like', '%'.$city.' %');
//            $query->orWhere('orders.data_shipping', 'like', '%'.$city.',%');
//          })
          ->where('orders.confirm', true)
          ->where('orders.created_at', '>', '2024-04-12 00:00:00');
    })->get();
    foreach($users as $user){
      echo $user->phone.'<br/>';
    }
    dd($users);
    (new OrderController())->findNotPaidOrders();
    dd(1);
    $users = User::permission('Доступ к админпанели')->pluck('id')->toArray();
    $orders = Order::query()->select(DB::raw('SUM(amount) as amount'), 'user_id')
        ->whereNotIn('user_id', $users)
        ->where('confirm', 1)
        ->where('created_at', '>', '2023-07-01')
        ->groupBy('user_id')
        ->orderByDesc('amount')
        ->limit(100)
        ->get();
    $i = 1;

    echo '<table>';
    echo '<thead><tr>';
    echo '<th>#</th>';
    echo '<th>Сумма</th>';
    echo '<th>Фио</th>';
    echo '<th>Email</th>';
    echo '<th>ID пользователя</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach($orders as $order){
      $user = $order->user;
      echo '<tr>';
      echo '<td>'.$i.'</td>';
      echo '<td>'.formatPrice($order->amount).'</td>';
      echo '<td>'.$user->name.'</td>';
      echo '<td>'.$user->email.'</td>';
      echo '<td>'.$user->id.'</td>';
      echo '</tr>';
      $i++;
    }
    echo '</tbody>';
    echo '</table>';
    dd($orders);
    UpdateBoxberryPvzsJob::dispatch(0)->onQueue('boxberry_pvz');
    $users = Voucher::query()
        ->select('vouchers.code')
        ->where('vouchers.available_until', '>', now()->format('Y-m-d H:i:s'))
        ->where('vouchers.used_at', '>', '2024-07-14 17:00:00')
        ->where('vouchers.used_at', '<', '2024-07-15 21:00:00')

        ->get();
    //$users = User::query()->whereIn('id', $vouchers)->get();
    dd($users);
//    $products = Product::query()->select('id')->where('category_id', 30)->pluck('id')->toArray();
//    $users = User::query()->select('id','email')
//        ->whereHas('orders', function (Builder $builder){
//          $builder->where('created_at', '>', '2024-01-01');
//          $builder->where('confirm', 1);
//        })
//        ->whereDoesntHave('orders', function(Builder $builder) use ($products){
//          $builder->where('created_at', '>', '2024-01-01');
//          $builder->where('confirm', 1);
//          $builder->whereHas('items', function (Builder $query) use ($products){
//            $query->whereIn('product_id', $products);
//          });
//        })
//        //->whereIn('id', [1,2])
//        ->chunk(500, function ($users) {
//          foreach($users as $user){
//            (new MailSender($user->email))->promo1();
//          }
//        });
    // dd($vouchers);
//    $numbers = [
//        '9002888072',
//        '9006417619',
//        '9039439930',
//        '9054942828',
//        '9094629538',
//        '9094673731',
//        '9170245913',
//        '9180768828',
//        '9180896896',
//        '9181117181',
//        '9181333516',
//        '9181569474',
//        '9181944585',
//        '9182561122',
//        '9182985133',
//        '9183172593',
//        '9183219658',
//        '9183777924',
//        '9183992359',
//        '9184380987',
//        '9184430021',
//        '9184546746',
//        '9186342481',
//        '9186622828',
//        '9186855060',
//        '9189620555',
//        '9189738221',
//        '9189784344',
//        '9220989999',
//        '9282823777',
//        '9298376581',
//        '9381258233',
//        '9384793698',
//        '9385000193',
//        '9493402822',
//        '9530854280',
//        '9531144527',
//        '9534122084',
//        '9605239493',
//        '9608874675',
//        '9615830421',
//        '9616551057',
//        '9620184835',
//        '9676664139',
//        '9676700012',
//        '9787694213',
//        '9828364600',
//        '9880805655',
//        '9883662151',
//        '9888880394',
//        '9892312771',
//        '9892958662',
//        '9896313728',
//        '9958634467',
//        '9965223766',
//        '9967070617',
//        '9994110192',
//        '9996317443',
//        '9996399300',
//        '9298130161',
//        '9654663699',
//        '9388663375',
//    ];
//
//    foreach($numbers as $number){
//      $user = User::query()->where('phone', 'like', '%'.$number)
//          ->whereHas('tgChats', function(Builder $builder){
//            $builder->where('active', true);
//          })->first();
//      if($user){
//        echo $number.'<br/>';
//      }
//    }
//    dd(1);
//      $count = (new UserController())->telegramGifts();
//      dd($count);
//      $tgChannel = new Client();
//      $mediaGroup = new MediaGroup();
//      $mediaGroup->setChatId(50810378);
//      $media1 = new Media();
//      $file_path = urlToStoragePath('https://lemousse.shop/storage/vouchers/N059-0S41-8691.png');
//      $media1->setType('document');
//      $media1->setMedia($file_path);
//      $media2 = new Media();
//      $file_path = urlToStoragePath('https://lemousse.shop/storage/vouchers/4925-V0NU-4NU8.png');
//      $media2->setType('document');
//      $media2->setMedia($file_path);
//
//      $mediaGroup->setMedia([
//          $media1->toArray(), $media2->toArray()
//      ]);
//      $mess = $tgChannel->sendMediaGroup($mediaGroup);
//      dd($mess);


      $users = User::query()->select('id', 'email')->whereRaw('MONTH(birthday) = ?', 6)
          ->whereRaw('DAY(birthday) = ?', 18)
          ->whereDoesntHave('bonus_transactions', function(Builder $builder){
            $builder->where('created_at', '>', now()->subYear()->format('Y-m-d H:i:s'));
            $builder->where('comment', 'birthday');
          })
          ->get();

      foreach($users as $user){
        $user->addBonuses(500, 'birthday');
        (new MailSender($user->email))->birthdayGreetings(500);
        foreach($user->tgChats as $tgChat){
          (new TelegramSender($tgChat))->birthdayGreetings(500);
        }
      }
    dd($users);

    $bonus = BonusTransaction::query()->where('amount', -250)->where('comment', 'like', 'Товар %')->sum('amount');
    dd($bonus);
//    $users = User::query()->whereIn('id', $user_ids)->get();
    $users = User::query()->has('comments')->get();
    $smm = 0;
    $bb = 0;
    $i = 0;
    $res = [];
    foreach($users as $user){
      $u_bonus = $user->getBonuses();
      $product_ids = $user->comments()->where('hidden', 0)->pluck('commentable_id');
      $products = Product::whereIn('id', $product_ids)->get();
      $extra_b = 0;
      foreach($products as $product){
        $doubles = $product->comments()->where('hidden', 0)->where('data->bonused', true)->where('user_id', $user->id)->count();
        if($doubles > 1){
          $orders = $user->orders()->where('data_cart', 'like', '%'.$product->sku.'%')->count();
//          echo '****<br/>';
//          echo $product->id.' - '.$doubles.' - '.(250 * ($doubles - 1)).'<br/>';
//          echo $user->id.' - '.$user->getBonuses().' - '.(250 * ($doubles - 1)).'<br/>';
//          echo '****<br/>';
          if($orders < $doubles && $u_bonus){
            $comment = $product->comments()
                ->where('hidden', 0)
                ->where('data->bonused', true)
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->first();
            if($comment){
              $user->subBonuses(250, 'Товар '.$comment->commentable->sku);
              $comment->update([
                  'hidden' => true,
                  'data' => [
                      'bonused' => false
                  ]
              ]);
              $i++;
            }

            $email = str_replace('@', '%40', $user->email);
            echo 'https://lemousse.shop/admin/products/reviews?email='.$email.'&product%5B%5D='.$product->sku.'<br/>';
            $extra_b += 250 * ($doubles - $orders);
            $smm += 250 * ($doubles - $orders);
          }
        }
      }
      if($extra_b){
        $res[] = [
            'name' => $user->email,
            'extra' => $extra_b,
            'bonuses' => $u_bonus,
        ];
//        $i ++;
//        $bb += $user->getBonuses();
//        echo $i.') '.$user->id.' - '.$user->getBonuses().' - '.($doubles - $orders).' - '.$extra_b.'<br/>';
      }

//      $bonuses = 0;
//      if($user->getBonuses() > 0){
//        $smm += $user->bonus_transactions()->where('amount', 250)->sum('amount');
//        echo $user->id.' - '.$user->bonus_transactions()->where('amount', '<', 0)->sum('amount').'<br/>';
//      }

    }

    dd(sortItemsByName($res, 'extra'), $smm, $i);
    dd();
    $products = Product::query()->has('comments')->get();
    $i = 0;
    $user_ids = [];
    foreach($products as $product){
      $comments = $product->comments()->where('created_at', '>', '2023-12-01')->get();
      foreach($comments as $comment){
        $doubles = $product->comments()->where('user_id', $comment->user_id)->count();
        if($doubles > 1){
          if(!in_array($comment->user_id, $user_ids)){
            echo $comment->user_id.'<br/>';
            $user_ids[] = $comment->user_id;
          }
          $i += $doubles-1;
        }
      }
    }
    dd($i, $user_ids);
    $city = 'ижний';
    $users = User::query()->whereHas('orders', function (Builder $builder){
      $builder
//            ->where('orders.city_id', 105)
          ->where(function ($query){
            $city = 'Казань';
            $query->where('orders.data_shipping', 'like', '%'.$city.' %');
            $query->orWhere('orders.data_shipping', 'like', '%'.$city.',%');
          })
          ->where('orders.confirm', true)
          ->where('orders.created_at', '>', '2024-04-12 00:00:00');
    })->get();
    foreach($users as $user){
      echo $user->phone.'<br/>';
    }
    dd($users);
//    $users = User::query()->select('id', 'email')->whereRaw('MONTH(birthday) = ?', [now()->month])
//        ->whereRaw('DAY(birthday) = ?', [now()->day])
//        ->whereDoesntHave('bonus_transactions', function(Builder $builder){
//          $builder->where('created_at', '>', now()->subYear()->format('Y-m-d H:i:s'));
//          $builder->where('comment', 'birthday');
//        })
//        ->get();
//    $i = 0;
//    foreach($users as $user){
//      $i++;
//      $user->addBonuses(500, 'birthday');
//      (new MailSender($user->email))->birthdayGreetings(500);
//      foreach($user->tgChats as $tgChat){
//        (new TelegramSender($tgChat))->birthdayGreetings(500);
//      }
//    }
//    dd($i);

    $this->removeCdekWebhook(1);
    dd(1);


    (new OrderController)->finishOrder($order->user, $order);
    dd($order);
      $orders = Order::query()
          ->where('status', '!=', 'refund')
          ->where(function($query){
            $query->where('data_shipping->cdek->uuid', '!=', null);
            $query->orWhere('data_shipping->cdek_courier->uuid', '!=', null);
          })
          ->whereHas('tickets', function(Builder $builder){
            $builder->where('tickets.created_at', '>', now()->subDays(10)->format('Y-m-d H:i:s'));
          })
          ->get();
    echo '<table>';
    echo '<thead><tr>';
    echo '<th>Номер заказа</th>';
    echo '<th>Трек</th>';
    echo '<th>Дата</th>';
    echo '<th>Фио</th>';
    echo '<th>Email</th>';
    echo '<th>Телефон</th>';
    echo '<th>Адрес</th>';
    echo '<th>Отправитель</th>';
    echo '<th>Заявленный размер посылки</th>';
    echo '<th>Статус</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach($orders as $order){
      $data = $order->data;
      $data_cart = $order->data_cart;
      $data_shipping = $order->data_shipping;

      $params = [];
      // формируем номер заказа
      $order_number = $order->getOrderNumber() . '_';
      foreach ($data_cart as $item) {
        $order_number .= 'm' . $item['id'] . '-' . $item['qty'] . '_';
      }
      $order_number = trim($order_number, '_');
      $comment = $order_number;
      if (mb_strlen($order_number) > 40) {
        $order_number = $order->getOrderNumber();
      }
      if (mb_strlen($comment) > 65) {
        $comment = $order->getOrderNumber();
      }

      $params['orderNumber'] = $order_number;
      // имя
      if (isset($data['form']['full_name'])) {
        $full_name = $data['form']['full_name'];
      } else {
        $full_name = $data['form']['last_name'] . ' ' . $data['form']['first_name'];
        if (isset($data['form']['middle_name']) && !empty($data['form']['middle_name'])) {
          $full_name .= ' ' . $data['form']['middle_name'];
        }
      }
      $params['comment'] = $comment;
      $params['full_name'] = $full_name;
      $params['email'] = $data['form']['email'];
      $params['phone'] = $data['form']['phone'];
      if ($data_shipping['shipping-code'] == 'cdek') {
        $params['address'] = $data_shipping['shipping-method'].': '.$data_shipping['cdek-pvz-id'].', '.$data_shipping['cdek-pvz-address'];
      } elseif ($data_shipping['shipping-code'] == 'cdek_courier') {
        $params['address'] = $data_shipping['shipping-method'].': '.$data_shipping['cdek_courier-form-address'];
      }

      echo '<tr>';
      echo '<td>'.$order_number.'</td>';
      echo '<td>'.($order->data_shipping['cdek']['invoice_number'] ?? $order->data_shipping['cdek_courier']['invoice_number'] ?? '-').'</td>';
      echo '<td>'.date('d.m.Y H:i:s', strtotime($order->created_at)).'</td>';
      echo '<td>'.$params['full_name'].'</td>';
      echo '<td>'.$params['email'].'</td>';
      echo '<td>'.$params['phone'].'</td>';
      echo '<td>'.$params['address'].'</td>';
      echo '<td>Нечаева Ольга Андреевна</td>';
      echo '<td>до 1кг 9.5x23.5x16.5см</td>';
      echo '<td>'.$order->getStatus()?->name.'</td>';
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
      dd($orders);
      $users = User::query()->whereHas('super_bonuses', function (Builder $builder){
        $builder->where('amount', '>', 0);
      })->get();
      foreach($users as $user){
        $bonuses = $user->getSuperBonuses();
        $user->subSuperBonuses($bonuses, 'Обнуление бонусов');
      }
      dd($users);
//    UpdateBoxberryPvzsJob::dispatch(0)->onQueue('boxberry_pvz');
//    dd(1);
//      $puzzleImages = PuzzleImage::query()->where('result_message', 'Пазл собран')->get();
//      foreach($puzzleImages as $puzzleImage){
//        $puzzleImage->update([
//            'is_correct' => true
//        ]);
//      }
//      dd($puzzleImages);
//    $puzzleClient = new Client();
//    $setToken = $puzzleClient->getToken();
//    if($setToken){
//      dd($puzzleClient->getMe());
//    }
//
//    dd('stop');
      $users = User::query()->whereHas('tgChats', function(Builder $builder){
        $builder->where('active', true);
      })
//          ->whereDoesntHave('orders', function (Builder $builder){
//            $builder->where('confirm', 1);
//            $builder->where('created_at', '>', '2024-05-10 00:00:00');
//          })
          ->whereHas('super_bonuses', function (Builder $builder){
            $builder->where('amount', '>', 0);
          })
//       ->whereIn('id', [1,2])
      ->pluck('id')->toArray();
      dd($users);

//      $users = User::query()->where('phone', 'like', '%)%')->get();
//      foreach($users as $user){
//        $phone = $user->phone;
//        $phone = preg_replace("/[^,.0-9]/", '', $phone);
//        $phone = preg_replace('/^(89|79|9)/', '+79', $phone);
//        if ($phone[0] == '9') {
//          $phone = '+7' . $phone;
//        }
//        $user->update([
//            'phone' => $phone
//        ]);
//      }
//      dd($users);
      $users = User::query()->whereHas('orders', function (Builder $builder){
        $builder
//            ->where('orders.city_id', 105)
            ->where(function ($query){
              $city = 'Казань';
              $query->where('orders.data_shipping', 'like', '%'.$city.' %');
              $query->orWhere('orders.data_shipping', 'like', '%'.$city.',%');
            })
            ->where('orders.confirm', true)
            ->where('orders.created_at', '>', '2024-04-12 00:00:00');
      })->get();
      foreach($users as $user){
        echo $user->phone.'<br/>';
      }
      dd($users);



      $statuses = Status::query()->where('finish', true)->pluck('key')->toArray();
      $statuses = array_merge($statuses, ['is_processing', 'is_waiting', 'was_sended_to_store']);

      $orders = Order::query()->where('data_shipping->shipping-code', 'pochta')
          ->where('status', '!=', null)
          ->whereNotIn('status', $statuses)
          ->where('data_shipping->pochta->barcode', '!=', null)
          ->where(function($query){
            $query->where('status_updated_at', '<', now()->subHours(12));
            $query->orWhere('status', 'was_processed');
          })
          //->where('created_at', '>', now()->subMonths(3))
          ->orderByDesc('created_at')
          ->paginate(10);
      dd($orders);

      $finish_statuses = [
          'was_sended_to_store',
          'cancelled',
          'refund',
          'test',
          'cdek_4',
          'boxberry_выдано',
          'is_ready',
          'cdek_delivered',
          'cdek_not_delivered',
          'cdek_5',
          'address_error',
          'has_error',
          'boxberry_Возвращено в ИМ'
      ];
      $statuses = Status::query()->whereIn('key', $finish_statuses)->get();
      foreach($statuses as $status){
        $status->update([
            'finish' => true
        ]);
      }
      Status::flushQueryCache();
      dd(1);
      $pochta = (new PochtaController())->tracking();
      dd(1);
      $cdekPvzs = CdekPvz::where('region_id', null)->where('city_id', null)->get();
      foreach($cdekPvzs as $pvz){
        $region = CdekRegion::where('region_code', $pvz->region_code)->first();
        $city = CdekCity::where('code', $pvz->city_code)->first();
        $params = [];
        if($region){
          $params['region_id'] = $region->id;
        }
        if($city){
          $params['city_id'] = $city->id;
        }
        $pvz->update($params);
      }
      dd($cdekPvzs);

//      $this->removeCdekWebhook('0c279660-77e6-43d7-98b1-a36722b6de40');
//      dd(1);

//      $users = User::query()->whereHas('tgChats', function(Builder $builder){
//        $builder->where('active', true);
//      })
//          //->whereIn('id', [1,2])
//      ->pluck('id')->toArray();
//
//      $mailing = MailingList::find(9);
//      $text = "*ТАКОГО НЕ ДЕЛАЕТ НИКТО\\!\\!\\!*\n\n";
//      $text .= "Только 24ч🔥\n\n";
//      $text .= "Наш клиентский день в LE MOUSSE 1 \\+ 1 \\= 3🎁\n";
//      $text .= "*\\(Где самый дорогой продукт в корзине идет в подарок\\)*\n\n\n";
//      $text .= "https://lemousse\\.shop\n\n\n";
//      $text .= "Акция распространяется на товары из одной категории💫";
//      $tgChats = TgChat::query()->with('user')->whereIn('user_id', $users)->where('active', true)->chunk(1, function ($tgChats) use ($text, $mailing) {
//        foreach($tgChats as $tgChat){
////          if($tgChat->user_id!=1){
////            continue;
////          }
//          $tgChat->user->mailing_list()->syncWithoutDetaching($mailing);
//          $tgChat->notify(new TelegramNotification($text, 'text_message', 'MarkdownV2'));
//        }
//      });

//      $text = "*КЛИЕНТСКИЙ ДЕНЬ*\n";
//      $text .= "🎁🎁*LE MOUSSE* 🎁🎁\n\n";
//      $text .= "1\\+1 \\= 3 🔥🔥🔥\n";
//      $text .= "Где *самый ДОРОГОЙ* продукт в корзине идет *в подарок*🎁\n\n";
//      $text .= "https://lemousse\\.shop\n\n";
//      $text .= "Наличие тает  на глазах\\!\n";
//      $text .= "Успей приобрести желанные продукты ❤️\n";
//      $text .= "\\_\\_\\_\\_\n";
//      $text .= "Le Mousse \\- с заботой о твоей коже\\.";

      dd($tgChats);
//      $users = User::query()->select('id', 'email')->whereRaw('MONTH(birthday) = ?', 4)
//          ->whereRaw('DAY(birthday) = ?', 1)
//          ->whereDoesntHave('bonus_transactions', function(Builder $builder){
//            $builder->where('created_at', '>', now()->subYear()->format('Y-m-d H:i:s'));
//            $builder->where('comment', 'birthday');
//          })
//          ->get();
//      foreach($users as $user){
//        $user->addBonuses(500, 'birthday');
//        (new MailSender($user->email))->birthdayGreetings(500);
//        foreach($user->tgChats as $tgChat){
//          (new TelegramSender($tgChat))->birthdayGreetings(500);
//        }
//      }
      dd($users);
      Product::flushQueryCache();
      dd(1);
//      $orders = Order::query()->whereIn('data_shipping->shipping-code', ['cdek', 'cdek_courier', 'boxberry'])
//          ->where('status', '!=', null)
//          ->whereNotIn('status', [
//              'is_processing',
//              'is_waiting',
//              ' was_sended_to_store',
//              'cancelled',
//              'refund',
//              'test',
//              'cdek_4',
//              'boxberry_выдано',
//              'is_ready',
//              'cdek_delivered',
//              'cdek_not_delivered',
//              'cdek_5',
//              'address_error',
//              'has_error',
//              'boxberry_Возвращено в ИМ'
//              ])
//          ->where(function($query){
//            $query->where('data_shipping->cdek->uuid', '!=', null);
//            $query->orWhere('data_shipping->cdek_courier->uuid', '!=', null);
//            $query->orWhere('data_shipping->boxberry->track', '!=', null);
//          })
//          ->where(function($query){
//            $query->where('status_updated_at', '<', now()->subHours(12));
//            $query->orWhere('status', 'was_processed');
//            // $query->where('status', 'was_processed');
//          })
//          ->where('created_at', '>', now()->subMonths(3))
//          ->orderBy('created_at')
//          ->paginate(10, ['*'], 'page', 1);
//      dd($orders);

      return view('test');
      (new UserController())->birthdayGifts();
      dd(1);
      $orders = Order::query()
          ->where('partner_id', 13)
          ->where('confirm', 1)
          ->whereBetween('created_at', ['2024-02-22 00:00:00', '2024-02-29 23:59:00'])
          ->get();
      echo '<table>';
      foreach($orders as $order){
        echo '<tr>';
        echo '<td>'.date('d.m.Y H:i:s', strtotime($order->created_at)).'</td>';
        echo '<td>'.$order->getOrderNumber().'</td>';
        $city = $order->city;
        if($city){
          echo '<td>'.$order->city?->name.'</td>';
        }else{
          if($order->data_shipping['shipping-code'] == 'cdek'){
            $pvz = CdekPvz::query()->where('code', $order->data_shipping['cdek-pvz-id'])->first();
            echo '<td>'.$pvz?->city.'</td>';
          }elseif($order->data_shipping['shipping-code'] == 'cdek_courier'){
            $cdek_city = CdekCity::query()->where('code', $order->data_shipping['cdek_courier-form-city'])->first();
            echo '<td>'.$cdek_city?->city.'</td>';
          }elseif($order->data_shipping['shipping-code'] == 'boxberry'){
            dd($order->data_shipping['shipping-code']);
          }
        }

        echo '<td>'.$order->getStatus()?->name.'</td>';
        echo '</tr>';
      }
      echo '</table>';
      dd($orders);
      $mailmessage = (new MailMessage)
          ->subject('Личная встреча c Ольгой Нечаевой')
          ->greeting('Добрый день!')
          ->line('Напоминаем вам, завтра 19 марта в 16:00 состоится встреча с Ольгой Нечаевой, основателем бренда Le Mousse.')
          ->line(new HtmlString('Важно ‼️ обязательно взять паспорт или водительские права, фото не подойдет, вас не пропустят.<br/>При опоздании есть вероятность что вас могут не пропустить, пожалуйста расчитайте свое время, с 15:30 до 15:55 будет работать пропускной режим.<br/>Вход в зал с 15:55<br/>———'))
          ->line('Как добраться:')
          ->line(new HtmlString('<b>На автомобиле:</b><br/>Необходимо заехать на подземную парковку в ТЦ «Афимолл». Парковка сектор С.'))
          ->line('Далее Вы поднимаетесь на лифте на 1 этаж, выходите в торговый центр и поворачиваете в переход к башням(после выхода из коридора, в котором находится лифт - вам налево), проходите вперёд и попадаете на -1 этаж башни Федерация, ориентир аптека «НеоФарм».')
          ->line('Проходите вперёд и поворачиваете налево, поднимаетесь на эскалаторе на 1 этаж , по указателю проходите к ресепшену башни Восток 1, ресепшен находится за French Bakery и Мос табак.')
          ->line(new HtmlString('Далее на ресепшене получаете пропуск(‼️при себе необходимо иметь паспорт или водительское удостоверение).<br/>Через турникет проходите к лифтам, на сенсорной панели выбираете «29 этаж» и садитесь в указанный на этом экране лифт.'))
          ->line('На 29 Этаже, вам понадобится центральная дверь, проходите двойные двери, поворачиваете налево, и по правой стороне первая белая дверь А 30. Вы на месте!')
          ->line('———')
          ->line('Если вы на метро или пешком:')
          ->line('Вам нужна станция «Выставочная», первый вагон из центра, поднимаетесь по лестнице, далее по указателям пройдите в ТЦ «Афимолл» на 2 этаж(прямо по длинному коридору).')
          ->line('На эскалаторе поднимаетесь на второй этаж ТРЦ «Афимол» к кафе UDC, поворачиваете налево и проходите поперёк торгового центра, выходите к башне Федерация(ориентир - «Банк ВТБ», «Азбука Вкуса», между ними - главный вход в башню.')
          ->line('Когда Вы войдете в саму башню, нужно идти по правой стороне к ресепшену Восток 1, Он находится напротив отделения банка «ВТБ» на 1 этаже, за French Bakery и Мос табак.')
          ->line('Далее на ресепшен Вам нужно показать документы, удостоверяющие личность, для получения пропуска. Через турникет проходите к лифтам, на сенсорной панели выбираете «29 этаж» и садитесь в указанный зелёным цветом на экране лифт.')
          ->line('На 29 этаже, вам понадобится центральная дверь, проходите двойные двери, поворачиваете налево, и по правой стороне первая белая дверь А 30. Вы на месте!')
          ->line(new HtmlString('<a href="'.asset('навигация_МОЦ_из_метро_А30_page-0001.jpg').'">навигация_МОЦ_из_метро_А30.jpg</a>'))
          ->line(new HtmlString('<a href="'.asset('Навигация_МОЦ_из_МЦК_А30_page-0001.jpg').'">Навигация_МОЦ_из_МЦК_А30.jpg</a>'))
          ->line(new HtmlString('<a href="'.asset('навигация_моц_МАШИНА_А30_page-0001.jpg').'">навигация_моц_МАШИНА_А30.jpg</a>'))
          ->line(new HtmlString('Телефоны для связи:<br/>+7 904 412-64-67 Екатерина<br/>+7 977 340-40-38 Юлия '))
          ->line('———')
          ->line('До встречи!');
      $emails = [
          'prokopieva_8787@mail.ru',
          'marina-media@list.ru',
          '1989antonina@gmail.com',
          'karinawork88@mail.ru',
          'alinkamichaelis1621@gmail.com',
          'seregina.sasha@icloud.com',
          'm.manera@mail.ru',
          'borodkina.o.o@gmail.com',
          'lena.kozina.01@inbox.ru',
          'ts803@bk.ru',
          'selihova_tanya@mail.ru',
          'nika21002@mail.ru',
          'safronovavictoria@yandex.ru',
          'margoshamir@mail.ru',
          'ksu_sandimirova@mail.ru',
          'zubkovaelena1985@gmail.com',
          'kolomiecz_k@mail.ru',
          'shatilova_milana@mail.ru',
          'bogdanova221b@mail.ru',
      ];
      foreach($emails as $email){
        // Notification::route('mail', $email)->notify(new MailNotification($mailmessage));
      }

      dd(1);
      $res = DB::update('UPDATE `products` SET `quantity`=`quantity`-1 WHERE `id` = 1183 AND quantity > 0;');
      Product::flushQueryCache();
      dd($res);
      $orders = Order::select('id', 'data_shipping')->where('data_shipping->country_code', '!=', null)->where('country_id', null)->get();

//      foreach($orders as $order){
//        $country = Country::where('options->pochta_code', $order->data_shipping['country_code'])->first();
//
//        if($country){
//          $order->update([
//              'country_id' => $country->id
//          ]);
//        }
//      }
      dd($orders);
      $user = User::find(1);
      $order = Order::find(318699);
      foreach($user->tgChats as $tgChat){
        (new TelegramSender($tgChat))->trakingMessage($order);
      }
      dd(1);
//      $mailing = MailingList::find(6);
//      $phones = [
//          '79000120214','79000390304','79000558185','79000642251','79000794202','79000890120','79001203686','79001242453','79001317071','79001976627','79002064657','79002238691','79002803549','79002948760','79003142607','79003257538','79003331350','79003701820','79003763320','79003777377','79003891695','79004007239','79004339119','79004445555','79004741487','79004802964','79004892174','79004952082','79005014087','79005202802','79005373845','79005462096','79005610063','79005704662','79005810546','79005881826','79005918711','79005936667','79006002258','79006099554','79006220652','79006270881','79006408654','79006411248','79006481271','79006497074','79006504985','79006549422','79006690728','79006755741','79006769286','79006905061','79009021217','79009134288','79009216094','79009276816','79009281540','79009354961','79009374557','79009416912','79009456428','79009467569','79009468915','79009511038','79009517725','79009719049','79009796070','79009853725','79010168093','79010354893','79010580506','79010836312','79010923905','79011148151','79011177713','79011230160','79011727996','79011879577','79011956472','79012074722','79012860291','79013393837','79013556380','79013621210','79014010171','79014075321','79014220301','79015065008','79015643831','79015952885','79016693936','79016955502','79017018052','79017108896','79017193943','79017296862','79017541614','79017699468','79017800512','79018517787','79019022728','79019938046','79020009365','79020034040','79020170402','79020544235','79020923605','79020931021','79020938657','79020945477','79020952747','79020959002','79021035128','79021286069','79021313316','79021344808','79021371871','79021554310','79021569509','79021733077','79021776039','79022045701','79022106351','79022192566','79022255655','79022525911','79022610150','79022641194','79022744474','79023134879','79023620532','79023626899','79023806075','79023808164','79023818229','79023883290','79023976034','79024080427','79024207669','79024288690','79024380887','79024685161','79024800510','79025116866','79025890521','79026139697','79026368019','79026372776','79026402699','79026492262','79026507175','79026530591','79026531771','79026533041','79026571042','79026583754','79026894053','79026911249','79026962488','79027021861','79027046669','79027062388','79027157598','79027400362','79027618229','79027705849','79027896172','79028125166','79028129452','79028188765','79028227967','79028254459','79028262333','79028296699','79028406124','79028455808','79028554858','79028768661','79028784914','79028821821','79029093880','79029115100','79029175096','79029251744','79029381175','79029445210','79029551020','79029859026','79030061204','79030084650','79030157526','79030274672','79030507677','79030743990','79030750130','79030764217','79031003724','79031211528','79031367754','79031405024','79031726752','79031731370','79032004241','79032062375','79032094353','79032122222','79032128671','79032405860','79032451739','79032737605','79033012719','79033176379','79033273760','79033275218','79033298284','79033495119','79033571370','79033572220','79033702437','79033723347','79033735708','79033737009','79033750061','79033761599','79033762921','79033774421','79034025318','79034093679','79034131037','79034538187','79034650703','79034673915','79034715614','79035003139','79035337058','79035418032','79035421300','79035429727','79035631525','79035772300','79036168604','79036272900','79037339239','79037570606','79037626072','79037645585','79038136923','79038231920','79038947070','79039353075','79039572848','79039757392','79040197177','79040303999','79040315523','79040402422','79040551390','79040671131','79040695033','79040806855','79041080189','79041164649','79041230352','79041310121','79041362602','79041384179','79041528376','79041738117','79041908269','79041963360','79042015359','79042216280','79042497016','79042579097','79042671123','79042702545','79042714258','79042793196','79042844263','79042964358','79042988328','79043471966','79043592210','79043943502','79043969468','79044012316','79044042560','79044046141','79044081861','79044090472','79044100330','79044103279','79044116764','79044151944','79044175279','79044189046','79044205049','79044229458','79044235796','79044244707','79044245124','79044252595','79044279833','79044288392','79044289889','79044298884','79044314303','79044315535','79044321799','79044339030','79044356959','79044486308','79044487885','79044518267','79044836932','79044858031','79044918034','79044925415','79045093001','79045137132','79045343265','79045959871','79045982598','79046088786','79046152558','79046208003','79046324977','79046491465','79046551000','79046578033','79046580848','79046685619','79046770346','79047509001','79047509638','79047525024','79047536325','79047778757','79047784372','79047827582','79047848266','79048200606','79048401387','79048506585','79048639712','79048647727','79049219001','79049612587','79049697130','79049973363','79050085473','79050087466','79050255511','79050314051','79050624014','79050643625','79050773236','79051053253','79051087066','79051305434','79051516029','79051590004','79051919443','79052454245','79052861108','79053116063','79053315316','79053316961','79053602648','79053653137','79053672753','79053894120','79053920680','79053923459','79053963535','79054090635','79054153929','79054337059','79054337306','79054437329','79054545124','79054560426','79054601105','79054603581','79054658060','79054827679','79054839951','79054867639','79054875033','79055568182','79055880604','79055989163','79056372738','79056606538','79056787277','79057036323','79057320393','79057327766','79057495858','79057533686','79057949486','79058538605','79058880696','79058951555','79059005927','79059047887','79059114574','79059161814','79059548070','79059878369','79059930892','79060515306','79060812669','79060951080','79061016983','79061222242','79061307700','79061663055','79061686490','79061688558','79061714999','79061722770','79061724111','79062508752','79062611367','79063030287','79063135175','79063241255','79063389323','79063538826','79063946106','79063999451','79064018041','79064040656','79064049656','79064053184','79064058474','79064109040','79064128141','79064233366','79064244627','79064291733','79064405618','79064411671','79064580930','79064784389','79065020205','79065176614','79065410742','79065651860','79066157039','79066223362','79066333326','79066420574','79066445658','79066448087','79067022545','79067665203','79067784331','79067977670','79067993378','79068112234','79068365518','79068436255','79068667778','79069273023','79069440736','79069441328','79069637708','79069643257','79069656438','79080011500','79080023710','79080093325','79080205537','79080263399','79080380668','79080513291','79080594650','79080633370','79080687545','79080745439','79080858977','79081015456','79081070032','79081325554','79081387382','79081412985','79081540110','79081559145','79081566855','79081571827','79081683192','79081872938','79081874662','79081990565','79082182478','79082185588','79082333395','79082345279','79082556948','79082752915','79082982857','79083054347','79083060648','79083062356','79083310225','79083356261','79083397863','79084077536','79084683173','79084708028','79084754499','79084767175','79085059131','79085087041','79085112169','79085510599','79085511176','79086043342','79086377800','79086509334','79086527426','79086546430','79086680773','79086800587','79086862069','79086906433','79086909188','79086934225','79086981044','79087028982','79087288731','79087707758','79087767303','79087886040','79087987559','79088047288','79088552068','79088600123','79088621740','79088704528','79088741214','79088795169','79089037944','79089616564','79090180804','79090319106','79090605416','79091039152','79091445677','79091889622','79091913647','79091993337','79092049776','79092090059','79092104133','79092435354','79092601631','79093014425','79093486999','79093584914','79093777374','79093799763','79093801008','79093828301','79093850631','79093862796','79093874212','79093911140','79093946168','79093972377','79093998500','79094008860','79094145201','79094316223','79094427086','79094443261','79094498223','79094524053','79094636105','79095218097','79095700716','79095777774','79096076180','79096147539','79096148339','79096686199','79096733834','79096891532','79097334652','79097386032','79097393838','79097516790','79097570079','79097636376','79097669986','79097799775','79097897555','79097960010','79098438924','79098672735','79098693019','79098833309','79099263201','79099316331','79099466661','79099512742','79099573976','79099605445','79099619092','79099664212','79099731747','79099948525','79100849963','79100879075','79101097580','79101225256','79101403792','79101803475','79101876436','79101965079','79102263058','79102573504','79102689827','79102776554','79102913822','79103240745','79103361676','79103757590','79103820937','79103967420','79104000336','79104206103','79104785570','79105155371','79105547461','79105632210','79105678524','79105810385','79105883948','79105905234','79106070978','79106167466','79106301687','79106570383','79106642043','79106918471','79107109592','79107110127','79107683694','79107988870','79108032590','79108145549','79108247552','79108590005','79108725450','79108941315','79109059345','79109075576','79109151333','79109203131','79109256604','79109414378','79109613428','79110040209','79110188306','79110269547','79110271266','79110299757','79110342180','79110361070','79110477598','79110631409','79110931128','79110965267','79111376615','79111393334','79111570077','79111607677','79111614110','79111850004','79112100464','79112157802','79112296264','79112510530','79112701597','79113237702','79113323998','79113364529','79113534330','79114006933','79114074681','79114378414','79114421677','79114631015','79114833196','79115134193','79115407266','79115562543','79115827095','79115840496','79116256935','79116258467','79116422436','79116713190','79116742621','79116832663','79117071463','79117073836','79117302453','79117621618','79117693537','79117698846','79117798810','79117895074','79118086656','79118125713','79118483928','79118549347','79118606371','79118704649','79118891845','79118989895','79119183874','79119478582','79119482828','79119525200','79119592623','79119610533','79119622664','79119654823','79119775944','79120039120','79120139509','79120153940','79120162022','79120261140','79120554581','79120694378','79120776887','79121066909','79121081279','79121217943','79121285633','79121540457','79121564598','79121673599','79121789991','79122438499','79122642473','79122849210','79123368726','79123549922','79123857994','79124058438','79124226571','79124417237','79124720121','79125426375','79125426573','79125445070','79125564692','79125697005','79125988379','79126115291','79126364915','79126367198','79126429278','79126569945','79126741782','79126772112','79126785600','79126793718','79126892236','79126935709','79127514017','79127515342','79127634543','79127761086','79127998515','79128014342','79128071841','79128821072','79128837067','79128843132','79128972801','79129006831','79129427583','79129842939','79130005195','79130076702','79130089232','79130143488','79130302038','79130361874','79130450869','79130547368','79130906324','79131056519','79131095309','79131252772','79131472494','79131654847','79131669451','79131686493','79131798263','79132069388','79132312201','79132447378','79132481658','79132546692','79132630453','79133032346','79133133071','79133333817','79133413737','79133592669','79133654716','79133766917','79133774214','79133899205','79134193167','79134255634','79134456391','79134722179','79134882644','79134975218','79135011575','79135046261','79135553036','79135558650','79136092589','79136208061','79136396783','79136758018','79137377475','79137408543','79137436314','79137465597','79137537628','79137556797','79137617079','79137638373','79137670350','79137729062','79137811746','79137830963','79137881086','79137984584','79138095510','79138184774','79138523797','79138622635','79138624111','79139054661','79139138250','79139303973','79139344917','79139587271','79139963502','79140241369','79140295441','79140364962','79140390929','79140631574','79141123809','79141224558','79141440064','79141503828','79141884114','79141912933','79141950333','79141979641','79142102791','79142163244','79142265487','79142437471','79143097740','79143098748','79143177566','79143200791','79143598303','79144190164','79144508736','79144515149','79144990208','79145015525','79145424616','79145576040','79145597231','79145644030','79145675877','79145786391','79145804471','79145908586','79146332830','79146467347','79146681464','79146701383','79146705211','79146780234','79146787785','79146799924','79146862782','79146988070','79147246226','79147300579','79147493416','79147609924','79147704060','79147942130','79148465256','79148588354','79148746472','79149654895','79149728256','79149791511','79150155382','79150164828','79150181959','79150355244','79150676952','79150681142','79150778345','79150994698','79151030967','79151379833','79151469924','79151542462','79151635067','79151646724','79152060055','79152070015','79152213384','79152246971','79152407741','79152541869','79152667917','79152794336','79152984808','79152985927','79153220494','79153228318','79153325712','79153469500','79153559814','79153743823','79154006601','79154118514','79154189173','79154475434','79155002387','79155323487','79155393832','79155648309','79155663030','79155952236','79155971778','79156159064','79156569583','79156613608','79157056380','79157143218','79157158790','79157327882','79157384516','79157575557','79157718791','79157875790','79158081597','79158297712','79158367728',
//          '79158494506','79158512739','79159080924','79159149118','79159440566','79159794179','79159967812','79160047782','79160128354','79160154372','79160204887','79160254882','79160271056','79160346044','79160795965','79160887075','79160950432','79161102115','79161142437','79161320911','79161481996','79161532159','79161571907','79161689556','79161794170','79161806060','79162678866','79162733371','79162736007','79162942182','79162992598','79163251098','79163254262','79163376114','79163401334','79163416021','79163488553','79163556055','79163723810','79163786748','79163835273','79163846477','79163944832','79164049208','79164082779','79164777547','79165270091','79165467633','79165565752','79165702846','79165776772','79165851387','79165873798','79165885428','79166101679','79166510165','79166642005','79167064674','79167161352','79167371154','79167903062','79167999352','79168006923','79168051626','79168232639','79168387889','79168515250','79168535402','79168593395','79168664391','79168869495','79168881171','79169109944','79169247884','79169299002','79169373458','79169514375','79170148347','79170248472','79170291010','79170297835','79170388688','79170666688','79170728058','79170871965','79170925568','79170938219','79171096574','79171423058','79171456587','79171562413','79171572396','79171687598','79171809202','79171859490','79172128434','79172348448','79172419603','79172482777','79172560944','79172675838','79172779909','79172890705','79172900310','79172986894','79173083093','79173091124','79173172746','79173184875','79173201164','79173230402','79173283173','79173293814','79173307373','79173322896','79173346113','79173347078','79173354585','79173432686','79173459380','79173709016','79173756275','79173853365','79173855402','79173868640','79173879207','79174360430','79174439034','79174472084','79174799573','79174808238','79174819691','79174959836','79175140382','79175182132','79175297333','79175444840','79175711045','79175772437','79175787166','79176186367','79176346424','79176371368','79176408210','79176450781','79176480278','79176498499','79176748063','79177292285','79177339690','79177372989','79177694991','79177837067','79177846880','79177949810','79177954269','79178354241','79178378240','79178387976','79178404808','79178419481','79178421073','79178454390','79178454952','79178460670','79178469818','79178719556','79178721033','79178976656','79178981115','79179123361','79179169856','79179419816','79179493859','79179527723','79179755767','79179806734','79180069572','79180188853','79180197673','79180377200','79180453060','79180498041','79180506616','79180570637','79180620650','79180666900','79180774814','79181039177','79181090889','79181228744','79181234163','79181313527','79181345854','79181368495','79181441335','79181512537','79181587975','79181641999','79181643726','79181731153','79181788554','79181827748','79181891613','79181917880','79182003334','79182090853','79182321337','79182480516','79182540102','79182604507','79182687997','79182762249','79182769075','79182891421','79182954226','79183276989','79183311127','79183437137','79183439273','79183647090','79183661111','79183706761','79183717570','79183929689','79184078649','79184103388','79184133707','79184239047','79184287924','79184301574','79184511361','79184718637','79184841178','79184961635','79184988109','79185119583','79185332266','79185471979','79185520979','79185680707','79185688719','79185734181','79185798844','79185847600','79186116118','79186169320','79186213845','79186390037','79186412002','79186471719','79186546668','79186648121','79186903383','79186934567','79187475764','79187525957','79187629043','79187652610','79187743757','79187763192','79187920892','79188200666','79188350685','79188606403','79188682894','79188819074','79188878422','79189256838','79189395987','79189436148','79189522988','79189538810','79189595571','79189613539','79189658895','79189675628','79189727997','79189742291','79189780110','79189833368','79189886602','79189943543','79189944885','79190248631','79191127398','79191250305','79191253822','79191319182','79191359224','79191468788','79191488491','79191670414','79191789991','79192070208','79192105725','79192134950','79192142127','79192340152','79192806255','79193003691','79193325620','79193569653','79193587858','79193681716','79193712275','79193878232','79193935206','79194070038','79194116353','79194344206','79194480435','79194514791','79194628774','79194758416','79195051934','79195128854','79195174969','79195242340','79195289078','79195440548','79195454437','79195482246','79195587164','79195789863','79195814528','79196111121','79196113743','79196144008','79196151852','79196186922','79196193662','79196216105','79196387571','79196697776','79196873541','79197513000','79197520516','79197561447','79197580529','79197651915','79197791695','79197909978','79197946162','79197962726','79197966653','79197973000','79197976498','79197988135','79198257727','79198265597','79198281952','79198442503','79198515870','79198560982','79198625714','79198693429','79199113966','79199252063','79199309144','79199503179','79199571960','79199765253','79199885700','79199929206','79199934807','79200063890','79200282240','79200402635','79200584542','79200624266','79200785452','79200919433','79201192818','79201313755','79201364727','79201512819','79201996212','79202110971','79202224710','79202352931','79202526307','79202938029','79203261454','79203305028','79203428230','79203441903','79203743424','79203971269','79204150215','79204207986','79204436776','79204442070','79204544228','79204571814','79204647321','79204691790','79205207279','79205363002','79205373657','79205649877','79205968358','79206033007','79206088247','79206154578','79206396653','79207510234','79207671715','79207809753','79208119069','79208164251','79208199915','79208428935','79208462356','79208518225','79209313684','79209533290','79209552071','79209557978','79209744027','79209914860','79210006761','79210099682','79210350235','79210464221','79212501119','79212964514','79213198211','79213260763','79213724799','79213741394','79214000992','79214002633','79214067238','79214232553','79214284670','79214414280','79214505751','79215581048','79215854270','79215872768','79216186913','79216330115','79216385390','79216501056','79216547334','79216688478','79216832290','79217193876','79217423949','79217482824','79217491663','79217515005','79217736439','79217751914','79218579882','79218627437','79218825316','79218914501','79219022190','79219262057','79219828010','79219838529','79220004111','79220023680','79220209528','79220272921','79220356326','79220474025','79220528854','79220530453','79220560249','79221035805','79221614310','79221921934','79222161710','79222546464','79222630593','79222648323','79222777020','79222811816','79222886662','79222978288','79223243064','79223262898','79223812851','79224052098','79224299812','79224361777','79224551895','79224636121','79224780069','79224793393','79225055336','79225261515','79225371240','79225447523','79225472585','79225775987','79225940231','79226143351','79226305868','79226309348','79226369054','79226517485','79226557247','79227010047','79227093727','79227192303','79227473900','79227523102','79227638350','79227747063','79228000400','79228393302','79228441264','79228700468','79228838747','79228986146','79229216120','79229588952','79229918168','79230389731','79230476157','79231222621','79231500555','79231513195','79231621707','79231684458','79231757735','79232146649','79232166599','79232215772','79232364658','79232400174','79232777793','79232892420','79233004847','79233087658','79233108781','79233151400','79233174323','79233343887','79233429033','79233739016','79233964102','79234039535','79234143595','79234198630','79234461892','79234999013','79235058210','79235087017','79235741191','79235831664','79236000768','79236098332','79236107021','79236129688','79236169861','79236222694','79236466125','79236493069','79236563668','79236709274','79237148942','79237158492','79237252555','79237597339','79237632221','79237725650','79237789523','79237825331','79241055470','79241207210','79241364376','79241601949','79241781367','79241913365','79241930918','79242260618','79242346988','79242351200','79242420274','79242451894','79242518081','79242619777','79242839289','79243136700','79243177718','79243253549','79243457336','79243500375','79244427731','79244514220','79244645694','79244670891','79244736104','79244827909','79245086617','79245268616','79245284766','79245689477','79246862211','79246953656','79247013087','79247241086','79247275352','79247277725','79247289099','79247903951','79248832992','79250014143','79250031026','79250201154','79250271559','79250367101','79250392008','79250425361','79250745085','79250759678','79251243912','79251411070','79251535765','79251748070','79252282517','79252285676','79252413077','79252670629','79253074139','79253114554','79253309467','79253330403','79253430703','79253756355','79254170722','79254297805','79254460805','79254532821','79254878783','79255883875','79256345994','79256781996','79256844649','79257515992','79257900366','79257956144','79258067743','79258361406','79258453583','79258554275','79258799454','79258835169','79258866961','79258871480','79258931007','79259064724','79260196005','79260336244','79260575056','79260968445','79261055213','79261155034','79261255982','79261404064','79261622545','79262016969','79262307723','79262877533','79263026413','79263155321','79263191498','79263239305','79263304575','79263316605','79263424692','79263496935','79263735210','79263895355','79263928808','79263964121','79264001811','79264374321','79264599493','79264782883','79264790989','79264917974','79265725718','79265757350','79265759971','79265883971','79265901782','79266038693','79266322445','79266524510','79266535070','79266666940','79266800999','79267238262','79267316626','79267839723','79267953435','79268471554','79268574209','79269113011','79269295450','79269514323','79269594347','79269736010','79269845854','79269880040','79270025977','79270147559','79270274288','79270308586','79270349015','79270371640','79270410991','79270544084','79270551724','79270605841','79270642666','79270643251','79270657755','79270699245','79270699372','79270817629','79270996611','79271040087','79271040357','79271043540','79271076407','79271237933','79271399965','79271447868','79271537694','79271555801','79271804446','79272194553','79272296935','79272554748','79272555770','79272571757','79272577984','79272713709','79273198308','79273355427','79273662033','79273842324','79274074766','79274245232','79274287476','79274391310','79274420340','79274736441','79274924268','79274954967','79275048564','79275061900','79275065680','79275076567','79275111888','79275112581','79275164542','79275180022','79275214001','79275218680','79275255709','79275282125','79275287490','79275292462','79275322717','79275330444','79275341832','79275367976','79275374855','79275377110','79275394326','79275398646','79275399968','79275440857','79275444973','79275457340','79275518348','79275590773','79275595699','79275741815','79275957350','79275986301','79276078523','79276079692','79276095605','79276252781','79276307297','79276403744','79276628028','79276789822','79276834828','79276983634','79277000687','79277301881','79277519827','79277589480','79277599190','79277638110','79277642972','79277730982','79277850203','79277958277','79278014002','79278167509','79278263733','79278277333','79278322321','79278419450','79278437510','79278561595','79278800069','79278854094','79278887837','79278915874','79278960497','79278979794','79279026248','79279080014','79279122007','79279551409','79279561273','79279613655','79279809276','79279981131','79280050720','79280122946','79280438493','79280530876','79281023459','79281124558','79281137595','79281177881','79281179580','79281208039','79281319091','79281380247','79281473800','79281521150','79281826214','79282055736','79282067967','79282308118','79282445752','79282627279','79282644011','79282927755','79283001056','79283066631','79283162363','79283244632','79283338390','79283453883','79283669136','79283788434','79284007384','79284150630','79284202609','79284242421','79284369764','79284730270','79285175428','79286061118','79286082913','79286096710','79286107021','79286133188','79286206668','79286749335','79286896862','79287627419','79287657569','79287676218','79288159597','79288641702','79289027449','79289052178','79289648277','79289649992','79290073069','79290498623','79290700611','79290779418','79292308477','79292559383','79292585892','79293083539','79293089970','79293326078','79293487946','79293691408','79294386910','79294569290','79295100942','79295714374','79295863072','79295999353','79296554092','79296585536','79297008000','79297075777','79297746411','79297779759','79297791443','79297814612','79297841678','79297876752','79298308785','79298414218','79298470016','79298568605','79298779458','79299313283','79299861664','79299881698','79301014697','79301241894','79301665816','79301674931','79303030311','79304120160','79304121122','79307216549','79307234145','79307475385','79308199117','79308214285','79308282829','79308941593','79308993132','79309169101','79309369916','79309371644','79309501401','79309587399','79309597153','79312125798','79312443304','79312500701','79312556763','79312761344','79312958964','79313086855','79313381091','79313788033','79315055185','79315314894','79315425343','79315778797','79315830133','79319001319','79319585421','79319678603','79319692699','79319980924','79320182557','79320994292','79322307014','79322320613','79322498164','79322558509','79324067666','79324099633','79324353801','79324814052','79325303642','79325500563','79325567641','79325580797','79326030830','79326236857','79330000598','79333285282','79333329630','79333352697','79364444093','79370126644','79370711047','79370809994',
//'79370820593','79370827894','79370842745','79370856049','79370856689','79370885777','79370889084','79370912285','79370936318','79370950215','79370951755','79370970478','79370979700','79371010816','79371036680','79371181018','79371204344','79371236227','79371318953','79371567318','79371577717','79371769647','79371826817','79371901216','79371955305','79372125115','79372370830','79372415150','79372554110','79372606527','79372639887','79372667747','79372672227','79372741163','79372761500','79372982140','79373025565','79373055123','79373208020','79373213253','79373357709','79373451626','79373581456','79373631302','79374002104','79374055939','79374108206','79374336660','79374729998','79374741734','79375200525','79375332891','79375334638','79375417280','79375450909','79375454613','79375470184','79375488322','79375489448','79375495131','79375496750','79375525551','79375558187','79375569854','79375594233','79375605913','79375653031','79375657841','79375823903','79375887408','79376330878','79376359620','79376434060','79376473922','79376921062','79376935636','79376937557','79376938886','79376964773','79376976101','79377000511','79377013331','79377057190','79377059234','79377075060','79377080073','79377081508','79377101711','79377164133','79377178635','79377180444','79377220798','79377247353','79377270342','79377302258','79377333306','79377346076','79377348968','79377369996','79377383838','79377384545','79377386222','79377391776','79377417620','79377424265','79377476995','79377500158','79377575777','79377756499','79378425330','79378430751','79378444336','79378612368','79378709086','79378879297','79379356838','79379938069','79381110038','79381110644','79381192525','79381220331','79381273067','79381288099','79381355989','79381499397','79381557357','79381696762','79381738999','79383040173','79383045039','79383131920','79383148642','79383581338','79384020355','79384225071','79384298323','79384753151','79384943854','79385024960','79385352512','79393737812','79393814930','79393827513','79393906999','79393926337','79393936477','79397088342','79397120916','79397501922','79397716153','79409666776','79493376960','79493667531','79493807151','79497200533','79500007552','79500022223','79500032433','79500138691','79500180637','79500265528','79500301916','79500302353','79500362666','79500371801','79500467505','79500508982','79500596656','79500766652','79500871360','79500949666','79501112326','79501378544','79501384159','79501392222','79501573525','79501879261','79502099330','79502148916','79502212099','79502479464','79502512951','79502650224','79502657279','79502701518','79502710316','79502864868','79502921186','79502924051','79502927234','79503135965','79503139080','79503182831','79503211018','79503216015','79503253964','79503610613','79503860095','79503965221','79504193890','79504297875','79504390369','79504403195','79504586483','79504589259','79504741573','79504785536','79504791879','79504807380','79504820242','79504880491','79504968537','79505080353','79505138702','79505265776','79505376291','79505425032','79505580318','79505588305','79505925397','79506007853','79506112035','79506217726','79506298514','79506338663','79506476705','79506530773','79506672660','79506712049','79506740851','79506749033','79506768088','79506785859','79506816494','79506833488','79506867397','79507036997','79507162437','79507222771','79507311405','79507549472','79507631614','79507678418','79507819108','79507955314','79508001905','79508053440','79508082836','79508118055','79508135301','79508283563','79508377524','79508450895','79508522511','79508648082','79508709840','79508725461','79508751252','79508765864','79508790777','79508908014','79509047669','79509068048','79509212326','79509247339','79509601983','79509690093','79509804049','79509942030','79509975334','79509976293','79510005760','79510120104','79510195365','79510306182','79510325597','79510373834','79510399926','79510574009','79510657994','79510874130','79511008584','79511037557','79511244729','79511328626','79511420671','79511548096','79511577952','79511608470','79511678653','79511767595','79511772443','79511948169','79512129822','79512606002','79512634396','79512708942','79512771163','79513116778','79513138140','79513141246','79513280836','79513647924','79513721772','79513751004','79513830220','79514116678','79514132369','79514166781','79514261166','79514424838','79514441589','79514447097','79514493911','79514623241','79514716797','79514740272','79514839520','79514871332','79514947008','79514947824','79514952996','79515034076','79515164633','79515264205','79515264776','79515290816','79515457953','79515625351','79515672660','79515752260','79515815674','79515846359','79515899720','79516456253','79516513100','79516641916','79516660650','79516750808','79516763450','79516768855','79516986551','79517000128','79517027454','79517145084','79517309501','79517334265','79517649815','79517738838','79517831218','79517939702','79518117229','79518293980','79518339858','79518348033','79518537533','79518565709','79518570648','79518746629','79518755023','79518870535','79518885558','79519223134','79519670982','79519757256','79519821545','79519874151','79519887030','79519960909','79520284841','79520367340','79520381352','79520445335','79520460590','79520568656','79520590651','79520675510','79520748021','79520781571','79520854842','79521013365','79521131333','79521133676','79521271322','79521387750','79521689099','79521836437','79522090067','79522150112','79522272198','79522414538','79522439521','79522511081','79522728813','79522768735','79522848665','79522967046','79523103535','79523136032','79523185118','79523294346','79523364562','79523466139','79523734328','79523782851','79523803718','79523884243','79523918653','79524077870','79524210155','79524271203','79524315511','79524460954','79524508258','79524565805','79524633870','79524719176','79524754128','79524926566','79525049988','79525104616','79525248341','79525266420','79525267194','79525313266','79525374280','79525722153','79525750704','79525796979','79525820201','79525853544','79525879087','79525949858','79525961499','79526101949','79526126372','79526478283','79526555564','79526590720','79526936226','79527378014','79527605784','79527612232','79527652896','79527655760','79527665286','79527681896','79527763747','79527769560','79527772722','79527792726','79527846545','79527923969','79527979561','79528016553','79528206388','79528244174','79528299448','79528335633','79528489394','79528542415','79528806180','79528853831','79528959055','79528988290','79529157698','79529162827','79529210448','79529290063','79529393772','79529445331','79529477009','79529496343','79529606359','79529664834','79529830659','79529991748','79530115263','79530154696','79530220025','79530225507','79530339579','79530415767','79530533441','79530649721','79530682913','79530728125','79530741492','79530837399','79530838347','79531015019','79531041253','79531046968','79531233517','79531257513','79531301187','79531348772','79531399386','79531554252','79531624762','79531631420','79531878778','79532329168','79532665571','79532776832','79532843866','79532969490','79533099511','79533165241','79533176320','79533191859','79533463439','79533608164','79533612150','79533638370','79533898015','79534006106','79534327894','79534335937','79534359311','79534459761','79534607364','79534655842','79534680030','79534731930','79534746479','79534830690','79534893278','79534923451','79534960519','79535165520','79535206512','79535240944','79535503457','79535527475','79535547542','79535617911','79535624113','79535678622','79535780436','79535834067','79535991996','79536083552','79536278517','79536723707','79536731637','79536770963','79536772244','79537159636','79537179983','79537527991','79537545596','79537678683','79538622675','79538652984','79538675480','79538834664','79538876243','79538972990','79538973917','79538992059','79539138593','79539151677','79539209902','79539341077','79539350989','79539389963','79539516375','79539538437','79539591399','79539596586','79539716036','79539728620','79539885120','79539994414','79582454929','79582802103','79583927271','79586228664','79586264092','79586290414','79586459219','79586704562','79586708779','79588809777','79591038361','79591090235','79591131630','79591251743','79591303467','79591458870','79591560875','79591711198','79595058855','79595203870','79595337526','79600016913','79600227766','79600428351','79600446212','79600545691','79600659251','79600695508','79600797814','79600929666','79600969229','79601044448','79601486009','79601640257','79601860094','79601934703','79602391412','79602435517','79602482797','79602552551','79603087231','79603268614','79603299505','79603363758','79603396900','79603521511','79603872924','79603916812','79604049842','79604361338','79604426207','79604427347','79604582623','79604653107','79604700030','79604722879','79604979034','79605362928','79605418107','79605701526','79606128334','79606210599','79606228516','79606238669','79606897477','79607048676','79607078068','79607411204','79607985666','79608028343','79608075820','79608164496','79608341283','79608554620','79608763640','79608767040','79608789042','79608800521','79608820960','79608853605','79608888775','79608899000','79608936793','79608937501','79608941294','79609221004','79609374880','79609408466','79609737706','79610007527','79610031041','79610256464','79610308724','79610572428','79610590674','79610611802','79610616144','79610703320','79610722052','79610733642','79610747029','79610787775','79610791634','79610873787','79610876956','79610902712','79610910360','79610912277','79611351046','79611525865','79611865154','79611877494','79611912272','79612280262','79612818585','79612906864','79612944456','79613068308','79613156890','79613189614','79613373311','79613449191','79613816502','79613978787','79614022862','79614104456','79614414868','79614625410','79614629692','79614640372','79614892911','79614910002','79614997276','79615206704','79615310999','79615490088','79615883949','79616238051','79616263626','79616560189','79616563553','79616564939','79616598339','79616615217','79616622215','79616634251','79616645201','79616660779','79616667894','79616693600','79616702321','79616726374','79616728684','79616779897','79616801737','79616827669','79616835544','79616845900','79616866841','79616900004','79616908848','79616917950','79616921121','79616925052','79616927223','79616933292','79616939909','79616942129','79616996761','79617003124','79617064902','79617514824','79617607276','79617981305','79618938720','79619304978','79619917916','79619950620','79620055665','79620497108','79620549736','79620775819','79620822464','79620898555','79620920413','79621118966','79621122211','79621169997','79621179508','79621196047','79621556612','79622211991','79622632048','79622757462','79622900135','79622917730','79622994019','79623134867','79623204266','79623883107','79623887726','79623947511','79624055690','79624190480','79624316944','79624444713','79624558686','79624739872','79625103493','79625133240','79625223787','79625257024','79625320519','79625393063','79625500668','79625553865','79625612784','79625828258','79625859522','79626214629','79626221084','79626295481','79626687292','79626804340','79627211893','79627257005','79627361636','79627534777','79627594777','79627686851','79627708325','79627734540','79627776796','79628093954','79628173907','79628179911','79628185926','79628208027','79628598342','79628694385','79628785132','79628840181','79628896213','79629337247','79629406004',
//'79629801352','79630116746','79630359995','79630410346','79630426347','79631051051','79631273277','79631313672','79631514571','79632426822','79632955902','79633760550','79633858872','79634182118','79634454766','79634840765','79635014371','79635091223','79635110971','79635330739','79635590040','79636083758','79636155141','79636391002','79636591782','79636639829','79636757930','79636939276','79637226167','79637685333','79637793828','79638083338','79638554304','79638762856','79638982222','79639200203','79639406238','79639480908','79639630492','79639710084','79639739391','79639771662','79640143227','79640815699','79641408786','79641420043','79641504934','79641675892','79641961556','79641978057','79641990805','79642426677','79642968623','79643758275','79644082957','79645095509','79645197094','79645551054','79645689037','79645805842','79645829739','79647074707','79647093019','79647190393','79647969242','79647985249','79648011885','79648271494','79648330535','79648587882','79648790335','79649049024','79649141424','79649451626','79649562326','79649582744','79649669269','79649742994','79649803817','79650187858','79650557701','79650699609','79650777446','79650802878','79651472541','79651716776','79651955925','79652455082','79652816008','79652827470','79653069001','79653231962','79653579167','79653596242','79653653860','79653800403','79653929004','79654069280','79654114065','79654325855','79654441243','79654632742','79655189875','79655538759','79655775979','79655860609','79655886369','79656085441','79656807766','79657581701','79657855993','79657973000','79659103177','79659385904','79659400691','79659761184','79659792000','79659937880','79660142636','79660801360','79661140127','79661209786','79661777010','79662877773','79663111089','79663366699','79663554466','79663612215','79667006101','79667555199','79667672369','79667895343','79669323229','79670119118','79670256160','79670428848','79670733429','79670929222','79671273587','79671906521','79671992989','79672390083','79672687912','79672811611','79672891410','79673062005','79673425049','79673775916','79674156130','79674428249','79674461982','79674526997','79674617283','79674724664','79676568335','79677137173','79677158841','79677464640','79677746731','79677753175','79677788806','79677907274','79677948194','79678054954','79678267713','79678519125','79678555778','79678615617','79678936977','79679712136','79680358092','79681140611','79681201550','79681853948','79681947580','79682201831','79682846295','79682850198','79683582565','79683597995','79683912950','79684077637','79684235775','79684743968','79685061329','79685545095','79685567887','79685985044','79686237675','79686482852','79686682277','79686696572','79687268177','79688336393','79688488734','79688878464','79689093803','79689373838','79689541339','79689777337','79689820205','79690117012','79690547622','79692859410','79692902217','79696150002','79696509556','79696552328','79696567555','79696583018','79697227925','79771075636','79771523953','79771609496','79771623715','79771726803','79771847069','79772520611','79772706590','79772750758','79772945499','79772994734','79772996990','79773174792','79773188018','79773335367','79773400887','79773408273','79773524540','79773540711','79773673519','79773936840','79774002724','79774168858','79774170066','79774212185','79774389506','79774745571','79774793933','79775109339','79775149276','79775424335','79775432987','79775641475','79775722806','79775804128','79775830703','79775924715','79775963536','79775981869','79776180037','79776205737','79776406196','79776624465','79776630896','79776691138','79777083414','79777145941','79777311301','79777344031','79777519749','79777658299','79777865507','79777880185','79778509060','79778864148','79778879432','79778890052','79778944577','79778960408','79779197502','79779198368','79779358553','79779409020','79779437305','79779461756','79779546536','79779560709','79779690172','79779711561','79779811530','79779886198','79779957299','79780823829','79780868501','79781106260','79781483610','79782571873','79787145435','79787370644','79787420271','79787469538','79787732452','79787759125','79787821935','79787960297','79788001746','79788172262','79788177560','79788446947','79788657237','79788667110','79788775056','79789399626','79800822883','79801047378','79802618609','79803003298','79803767020','79804099022','79804368734','79805111514','79805414966','79805434303','79805470841','79805554147','79806405463','79806597769','79807078134','79807394162','79807824836','79811033997','79811055021','79811264609','79811759886','79811774112','79811942209','79811957115','79812184321','79813053999','79813517131','79814385565','79814562544','79816989293','79817209058','79817363099','79817458267','79817539525','79817548390','79817624790','79817746691','79817882496','79818500632','79818799318','79818813820','79818864460','79819043254','79819196128','79819370991','79819581781','79819779190','79821087124','79821095658','79821322093','79821389005','79821584249','79821621146','79821643343','79821794081','79821858577','79822035120','79822068571','79822093239','79822196872','79822551310','79822596829','79822820367','79822866065','79822873507','79823027131','79823123318','79823147558','79823211403','79823215129','79823632182','79824112858','79824209959','79824661862','79824666360','79824719779','79825041477','79825512874','79825917708','79825985234','79826272697','79826333217','79826443321','79826618762','79826904119','79826933467','79827484167','79827791065','79827927812','79827963530','79828005693','79828115870','79828212710','79829814030','79829914946','79829977180','79829979476','79831233505','79831340610','79831776969','79831857698','79831863591','79831999291','79832097545','79832389052','79832731104','79832738245','79832966582','79833024959','79833151509','79833155265','79833179421','79833264843','79833397767','79833640629','79833952558','79834320218','79834327179','79834400557','79834591558','79835071246','79835569966','79835622532','79835667917','79835669836','79835762952','79835865996','79836007930','79836390603','79841427397','79841609692','79841737668','79842631069','79842782850','79842796140','79850009757','79850094154','79850121982','79851135113','79851190402','79851232136','79851419508','79851689385','79851919943','79851932483','79851979681','79852035965','79852075318','79852079739','79852280885','79852389508','79852454447','79852563756','79852575466','79852656335','79852819815','79852975965','79853122317','79853185092','79853340840','79853659886','79853683164','79853691632','79853827990','79853974586','79854178103','79854247402','79854359776','79855860918','79856165124','79856948989','79857235439','79857483338','79857489199','79857806957','79857990000','79858123876','79858179385','79859493202','79859798079','79866669989','79867220161','79867234319','79867282562','79867605644','79867615780','79867865082','79867982984','79869018862','79869083276','79869092554','79869235919','79869319315','79869535941','79869615852','79869718861','79870003905','79870006497','79870011729','79870290312','79870322343','79870578623','79870593550','79870783260','79870908028','79870981082','79871120079','79871184827','79871344899','79871394542','79871652108','79871808755','79871840399','79872146078','79872147455','79872357849','79872381115','79872426008','79872538327','79872573619','79872586048','79872923555','79873151489','79873188886','79873463835','79873522962','79873555989','79873896899','79873903359','79874084387','79874105220','79874112540','79874161867','79874184949','79874227889','79874408671','79874475072','79874971240','79875198315','79875233114','79875303468','79875495746','79875746663','79876080478','79876463197','79876510494','79876739053','79877016996','79877074086','79877243218','79877411344','79877444808','79877914874','79878329091','79878404429','79878704189','79878806569','79878879485','79879152826','79879266920','79879278925','79879394910','79879833359','79880023181','79880030003','79880071484','79880094797','79880097025','79880122648','79880182558','79880215206','79880218459','79880222392','79880297887','79880317579','79880378556','79880536707','79880555393','79880834896','79880876743','79880886141','79880933071','79881032259','79881414149','79881418961','79881425335','79881588881','79882055981','79882504810','79882577720','79882715527','79883130861','79883192233','79883199023','79883388380','79883426018','79883478194','79883484518','79883667691','79883687908','79883694210','79883911154','79883992195','79884013442','79884916116','79884941343','79885017447','79885039489','79885370638','79885385427','79885435904','79885446681','79885478297','79885607387','79885676507','79885810483','79885834758','79885852018','79885868336','79885909295','79885988791','79886291969','79886724375','79886731490','79886774114','79887097440','79887124763','79887324768','79887330915','79887366788','79887417055','79887533789','79888542115','79888957051','79889088102','79889422426','79889428733','79889459603','79889474730','79889652114','79889821464','79889833380','79889845518','79889906036','79889925283','79889947136','79889954303','79889959693','79891350081','79892125662','79892899407','79894556934','79895015937','79895174264','79895215482','79895294351','79895320767','79895330141','79896146343','79896162012','79896175572','79897035112','79897086248','79897097973','79897284197','79897437112','79897922900','79898041138','79898060842','79898105541','79898289471','79898331464','79898375815','79898923783','79899740364','79899855195','79899887088','79899911141','79900097750','79900944096','79911114781','79911184365','79911571816','79911771621','79911999486','79912473587','79912945892','79913008220','79913388466','79914581011','79916570500','79917069399','79917922502','79918713433','79918756608','79920011390','79920412550','79921490815','79921496170','79921757288','79921947976','79922070723','79922107555','79922232265','79922297579','79923138612','79923465094','79924022030','79930037543','79930240694','79933823498','79934122915','79935579190','79940041101','79940136415','79941027840','79941340903','79950899981','79951085015','79951877247','79951903935','79953008449','79954026186','79954043045','79954043305','79954099685','79954119454','79954128040','79954136682','79954138319','79954180155','79954198913','79954202412','79955464740','79955523630','79956551378','79956789058','79957856449','79959012208','79959044857','79959178187','79959460385','79960230477','79960234737','79960248462','79960249374','79960276834','79960349892','79960667125','79961022093','79961033587','79961159030','79961245520','79961274745','79961841869','79961857920','79962208740','79962398653','79962418289','79962472058','79962523618','79962559717','79962680846','79962944765','79963077097','79963099750','79963116629','79963130664','79963133984','79963473565','79963484964','79963530293','79963538059','79963554148','79963567911','79963785861','79963792478','79963805274','79963854005','79963858430','79963887863','79963945371','79963951214','79963966164','79964030752','79964032163','79964070317','79964112589','79964149137','79964156592','79964162847','79964182451','79964199619','79964202717','79964302091','79964416609','79964429859','79964480609','79964495015','79964515539','79964716595','79964722397','79964728239','79964783310','79964839840','79964839892','79964855629','79964858343','79965023697','79965091636','79965099881','79965101009','79965223766','79965225538','79965441304','79965592598','79965618420','79965630562','79965688090','79965815983','79965926322','79966119862','79966174868','79966223369','79966249649','79966308502','79966364640','79966390067','79966423478','79966485514','79966940864','79967238400','79967256077','79967331020','79967431346','79967718622','79968018060','79969000727','79969137602','79969181545','79969236884','79969256744','79969261363','79969268044','79969273961','79969317212','79969378922','79969393810','79969409076','79969459374','79969514493','79969517828','79969605760','79990300588','79990347292','79990577110','79990717797','79990822701','79990999716','79991218786','79991317898','79991339750','79991450415','79991515582','79991584836','79991622161','79991640024','79991643680','79991653186','79991654269','79991725466','79991774021','79991850098','79991945932','79992025726','79992036013','79992255341','79992264039','79992291658','79992388349','79992448934','79992477996','79992551715','79992603575','79992605004','79992948888','79993388021','79993498916','79993601636','79993767440','79993789287','79993794418','79993795309','79994030217','79994105995','79994434367','79994478217','79994480215','79994483708','79994569404','79994584596','79994591912','79994626532','79994638869','79994684300','79994702818','79994704635','79994752794','79994800625','79994972365','79995136971','79995192555','79995193596','79995232546','79995299909','79995324984','79995387707','79995592532','79995598954','79995639428','79995659016','79995687601','79995690289','79995706840','79995713052','79995846009','79995850613','79995852044','79995854600','79995864066','79995872271','79995881583','79996022109','79996055276','79996056176','79996104220','79996104365','79996225954','79996247424','79996248869','79996250154','79996252880','79996253360','79996255405','79996256719','79996257714','79996261068','79996272765','79996282170','79996299078','79996377478','79996380987','79996381045','79996431319','79996457177','79996465260','79996496146','79996560406','79996913217','79996927758','79996938324','79996939150','79996965984','79996969727','79996987398','79996995018','79996995057','79997007763','79997051093','79997108468','79997252744','79997351490','79997352290','79997447716','79997843162','79997876551','79997903393','79997916224','79998002910','79998058934','79998162095','79998289624','79998294185','79998382170','79998443491','79998513492','79998701298','79998896129','79999689832','79999700267','79999731531','79999808621','79999814375','79999885614','79999899235','79999951055',
//
//      ];
//      $users_count = 0;
//      $limit = 0;
//      $phones = array_slice($phones, 3500, 500);
//      foreach($phones as $phone){
//        $phone = mb_substr($phone, 1);
//        $user = User::where('phone', 'like', '%'.$phone)->first();
//        if($user){
//          $user->mailing_list()->syncWithoutDetaching($mailing);
//          $users_count++;
//        }
//        $limit++;
//      }
//      dd(count($phones), $users_count);


      dd(now()->addHours(2)->toTimeString('minutes'));
//      $tgChat = TgChat::find(1);
//      $reply_message = Setting::query()->where('key', 'tg_notifications_reply')->first();
//      $relpy_today = $tgChat->tgMessages()
//          ->where('text', 'like', '%Данный бот, к сожалению, не отвечает на входящие сообщения%')
//          ->where('created_at', '>', now()->startOfDay()->format('Y-m-d H:i:s'))
//          ->exists();
//      echo '<pre>'.$reply_message->value.'</pre>';
//      dd($relpy_today, nl2br($reply_message->value));
//      $order = Order::find(321611);
//      // $order = Order::find(321579);
//      $order->update([
//          'confirm' => 1
//      ]);
//      (new OrderController)->finishOrder($order->user, $order);
//      dd('finish', $order->id);
////      Log::debug(2331);
//      $tgChannel = new Client();
//
//      $mediaGroup = new MediaGroup();
//      $mediaGroup->setChatId(50810378);
//
//      $media1 = new Media();
//      $file_path = urlToStoragePath('https://lemousse.shop/storage/vouchers/N059-0S41-8691.png');
//      $media1->setType('document');
//      $media1->setMedia($file_path);
//      $media2 = new Media();
//      $file_path = urlToStoragePath('https://lemousse.shop/storage/vouchers/4925-V0NU-4NU8.png');
//      $media2->setType('document');
//      $media2->setMedia($file_path);
//
//      $mediaGroup->setMedia([
//          $media1->toArray(), $media2->toArray()
//      ]);
//      $mess = $tgChannel->sendMediaGroup($mediaGroup);
//      dd($mess);
//      Notification::extend('telegram', function ($app) {
//        Log::debug('telegram 2');
//        return new TelegramChannel();
//      });
//      dd(123);
      $user = User::find(2);
      foreach($user->tgChats as $tgChat){

        (new TelegramSender($tgChat))->productNotification(Product::find(1181));
//        dd(1);
//        $tgChat->notify(new TelegramNotification('Тестовое сообщение'));
      }
      dd(1);
            $prizes = GiftCoupon::query()->where('prize_id', '!=', 146)
          ->where('created_at', '>', '2024-02-28 10:00:00')
          ->count();
      dd($prizes);
      $this->prizesProbabilities();
      dd(1);
      $orders = Order::query()
          ->where('data->total', '>=', 7000)
          ->where('created_at', '>=', '2024-02-29 00:00:00')
          ->where(function($query){
            $query->where('status', '!=', 'refund');
            $query->orWhere('status', null);
          })
          ->where('confirm', 1)
          ->get();

      //$orders = Order::has('giftCoupons')->where('created_at', '>', '2023-11-13 10:00:00')->where('confirm', 0)->get();
      $i = 0;
      foreach($orders as $order){
//        $data_cart = $order->data_cart;
//        $data_cart[] = [
//            'id' => 58,
//            'qty' => 1,
//            'name' => 'Пенка для интимной гигиены (подарок)',
//            'image' => 'https://dlyakojida.ru/storage/photos/shares/products/foamintim/compressed/penka_ig-fotor-20231112132328-200.jpg',
//            'model' => 'foamintimgift',
//            'price' => 0
//        ];
//        $order->update([
//            'data_cart' => $data_cart
//        ]);
//        if($order->giftCoupons()->exists()){
//          $i;
//          $order->giftCoupons()->delete();
//          continue;
//        }
        if($order->giftCoupons()->count()<3){
          $i++;
          echo $order->id.'<br/>';
          (new HappyCoupon())->setPrizeToOrder($order);
        }
//        if($order->giftCoupons()->where('prize_id', 22)->count()==3){
//          $i++;
////          $gift = $order->giftCoupons()->where('prize_id', 22)->where('data->position', null)->first();
////          if($gift){
////            $i++;
////            //dd($gift, $order);
////            $gift->delete();
////            (new HappyCoupon())->setPrizeToOrder($order);
////          }
//
//        }
      }
      dd($i, $orders);
//      $orders = Order::query()
//          ->where('created_at', '>', '2024-02-22 10:00:00')
//          ->where('confirm', 1)
//          ->where('partner_id', 13)
//          ->where(DB::raw('lower(data->"$.promocode.code")'), 'like', '%spb%')
//          //->where('data->promocode->code', 'like', 'Spb')
//          ->get();
//      dd($orders);
//      $prizes = GiftCoupon::query()->where('prize_id', '!=', 146)
//          ->where('created_at', '>', '2024-02-28 10:00:00')
//          ->count();
//      dd($prizes);
////      $order = Order::find(302212);
////      // (new HappyCoupon())->setPrizeToOrder($order);
////      for($i = 0;$i < 1000;$i++){
////        (new HappyCoupon())->setPrizeToOrder($order);
////      }
////      dd(1);
//      $mailing = MailingList::find(4);
//      $phones = [
//          '79000990630',
//      ];
//      $users_count = 0;
//      $limit = 0;
//      $phones = array_slice($phones, 1800, 300);
//      foreach($phones as $phone){
//        $phone = mb_substr($phone, 1);
//        $user = User::where('phone', 'like', '%'.$phone)->first();
//        if($user){
//          $user->mailing_list()->syncWithoutDetaching($mailing);
//          $users_count++;
//        }
//        $limit++;
//      }
//      dd(count($phones), $users_count);

//      User::query()->where('uuid', null)->chunk(5000, function ($users) {
//        foreach ($users as $user) {
//          $user->uuid = (string) Str::uuid();
//          $user->save();
//        }
//      });
//      dd(123);
      $orders = User::query()
          ->whereHas('raffle_members', function(Builder $builder){
              $builder->whereBetween('created_at', ['2023-09-01 00:00:00', '2023-09-30 23:59:59']);
          })
          ->whereDoesntHave('giftCoupons', function(Builder $builder){
              $builder->whereBetween('created_at', ['2024-02-01 00:00:00', '2024-02-29 23:59:59']);
          })
          ->doesntHave('mailing_list')
          ->orderByDesc('created_at')
          ->paginate(1000);

      foreach($orders as $order){
        echo $order->phone.',';
      }
      dd($orders);

      $order = Order::find(302212);
      (new HappyCoupon())->setPrizeToOrder($order);
//      for($i = 0;$i < 3000;$i++){
//        (new HappyCoupon())->setPrizeToOrder($order);
//      }
      dd(1);
      $data = [
          'AFL' => 'pickup_msk',
          'MRM' => 'pickup_vlg',
          'PER' => 'pickup_per',
          'KAP' => 'pickup_msk2',
          'VLZ' => 'pickup_vlzh',
          'SMR' => 'pickup_smr',
          'KRD' => 'pickup_krd',
          'KZN' => 'pickup_kzn',
          'SPB' => 'pickup_spb',
          'UFA' => 'pickup_ufa',
          'NGR' => 'pickup_nn',
          'NSK' => 'pickup_nsk',
          'TMN' => 'pickup_tmn',
      ];
      $storeCoupons = StoreCoupon::where('pickup_id', null)->get();
      foreach($storeCoupons as $storeCoupon){
        $code = substr($storeCoupon->code, 0, 3);
        $pickup = Pickup::where('code', $data[$code])->first();
        if(!$pickup){
          dd($storeCoupon);
        }
        $storeCoupon->update([
            'pickup_id' => $pickup->id
        ]);
      }
      dd($storeCoupons);

      $order = Order::find(302212);
      //(new HappyCoupon())->setPrizeToOrder($order);
      for($i = 0;$i < 3000;$i++){
        (new HappyCoupon())->setPrizeToOrder($order);
      }
      dd(1);
      (new StoreCouponController)->makeStoreCoupons();
      dd(123);

      $orders = Order::whereIn('data_shipping->shipping-code', ['cdek', 'cdek_courier', 'boxberry'])
          ->where('status', '!=', null)
          ->whereNotIn('status', ['is_processing', 'is_waiting', ' was_sended_to_store', 'is_assembled', 'cancelled', 'refund', 'test', 'cdek_4', 'boxberry_выдано', 'is_ready', 'cdek_delivered', 'cdek_not_delivered', 'cdek_5', 'address_error', 'has_error'])
          ->where(function($query){
            $query->where('data_shipping->cdek->uuid', '!=', null);
            $query->orWhere('data_shipping->cdek_courier->uuid', '!=', null);
            $query->orWhere('data_shipping->boxberry->track', '!=', null);
          })
          ->where(function($query){
            $query->where('status_updated_at', '<', now()->subHours(12));
            $query->orWhere('status', 'was_processed');
          })
          ->where('id', '>', 288597)
          ->where('id', 297766)
          ->paginate(50, ['*'], 'page', 1);
      dd($orders);
      foreach($orders as $order){
        $data_shipping = $order->data_shipping;

        if ($data_shipping['shipping-code'] == 'boxberry' && isset($data_shipping['boxberry']['track']) && $order->status != 'refund') {
          $status = (new BoxberryController())->checkStatus($order);
          dd($order, $status);
        } elseif (isset($data_shipping['cdek']['uuid']) || isset($data_shipping['cdek_courier']['uuid'])) {
          $status = (new CdekController)->checkStatus($order);
          dd($order, $status);
        }
      }
      dd($orders->first());
      $order = Order::find(301190);
      (new MailSender($order->user->email))->confirmOrder($order, $order->user);
      dd(1);
      $vouchers = Voucher::query()->where('available_until', '2024-02-01 00:00:00')->paginate(5000);
      foreach($vouchers as $voucher){
        $voucher->update([
            'available_until' => '2024-02-01 23:59:59'
        ]);
      }
      dd($vouchers);
      $orders = Order::query()
          ->whereBetween('created_at', ['2023-11-05 00:00:00', '2024-01-31 23:59:59'])
          ->where('confirm', 1)
          ->orderByDesc('created_at')
          ->paginate(1000);
      foreach($orders as $order){
        $cart = $order->data_cart;
        foreach($cart as $item){
          $orderItem = OrderItem::setParams($order->id, $item);
        }
//        $order->update([
//            'data_cart' => $cart
//        ]);
//        $orderItem = OrderItem::setParams($order->id, $item);
      }
      dd($orders);
      $order = Order::find(299039);
      $cart = $order->data_cart;

      $cart_chunk = array_chunk(mergeItemsById($cart), 5);
      $has_builder = false;
      foreach($cart_chunk as $cart_chunk_item){
        $item_code = '';
        foreach ($cart_chunk_item as $item) {
          if($item['id'] > 1000){
            $item['id'] -= 1000;
          }
          $item_code .= 'm' . $item['id'] . '-' . $item['qty'].', ';
        }
        echo $item_code.'<br/>';
      }

      dd($cart, mergeItemsById($cart));
      $orders = Order::query()
          ->whereBetween('created_at', ['2023-12-15 00:00:00', '2023-12-17 23:59:59'])
          ->where('status', 'refund')
          ->where('confirm', 1)
          ->where('partner_id', 13)
          ->count();
      dd($orders);
      $mailingList = MailingList::find(3);

      if ($mailingList) {
        $mailingList->users()->syncWithoutDetaching([336039,336034,336030]);
      }
      dd(123);



      $cdek_cities = CdekCourierCity::paginate(5000);
      foreach($cdek_cities as $city){
        $region = null;
        $cdek_region = $city->cdek_region;
        if($cdek_region){
          $region = $cdek_region->lm_region;
        }
        if(!$region){
          continue;
        }
        $country = $region->country;
        if(!$country){
          continue;
        }
        $db_city = City::query()->where('name', $city->city)->where('region_id', $region->id)->first();
        if(!$db_city){
          continue;
        }
        $city->update([
            'country' => $country->name,
            'lm_country_id' => $country->id,
            'lm_city_id' => $db_city->id,
            'lm_region_id' => $region->id,
        ]);
      }
      dd($cdek_cities);
//      $cdek_cities = CdekRegion::where('country_code', 'RU')->get();
//      foreach($cdek_regions as $region){
//        $db_region = Region::query()->where('name', $region->region)->first();
//        if(!$db_region){
//          $db_region = Region::create([
//              'name' => $region->region,
//              'country_id' => 1,
//          ]);
//        }
//        $region->update([
//            'lm_country_id' => 1,
//            'lm_region_id' => $db_region->id
//        ]);
//      }
//      dd($boxberry_regions);
//      $orders = Order::query()->where('confirm', 1)->where('data->double', null)->where('data_shipping->shipping-code', '!=', 'pickup')->where('status', '!=', 'refund')->whereBetween('created_at', ['2024-01-17', '2024-01-31'])->get();
//      $i = 0;
//      foreach($orders as $order){
//        $cart = $order->data_cart;
//        $qty = 0;
//        foreach($cart as $item){
//          if($item['id']!=1125){
//            $qty += $item['qty'];
//          }
//        }
//        if($qty == 1&&$order->data_shipping['price'] == 0){
//          $i++;
//        }
//      }
//      echo $i;
////      Content::flushQueryCache();
////      CompressContentImagesJob::dispatch(1)->onQueue('compressImages');
////      dd(1);
//
//      dd(123);
      $orders = Order::query()->select('data->form->phone as phone')->where('confirm', 1)->where('data_shipping->country_code', 643)->whereBetween('created_at', ['2023-11-01', '2023-11-30'])->get();
      $phones = [];
      foreach($orders as $order){
        if(!in_array($order->phone, $phones)){
          $phones[] = $order->phone;
        }
      }
      $users = User::permission('Доступ к админпанели')->get();
      foreach($users as $user){
        $phoneNumber = preg_replace('/^\+?7|8/', '', $user->phone);
        if(!in_array($phoneNumber, $check_arr)){
          $check_arr[] = $phoneNumber;
        }
      }
      $array = $phones; // Ваш исходный массив
      foreach($array as $key => $phone){
        $phoneNumber = preg_replace('/^\+?7|8/', '', $phone);
        if(in_array($phoneNumber, $check_arr)){
          unset($array[$key]);
        }
      }

//      $numberOfItemsToRemove = min(1000, count($array)); // Удаляем 1000 или меньше, если в массиве меньше элементов
//
//      if ($numberOfItemsToRemove > 0) {
//        $keys = array_keys($array); // Получаем ключи массива
//        $randomKeys = array_rand($keys, $numberOfItemsToRemove); // Получаем случайные ключи
//
//        if ($numberOfItemsToRemove === 1) {
//          // В случае, если array_rand возвращает один ключ, а не массив
//          unset($array[$keys[$randomKeys]]);
//        } else {
//          foreach ($randomKeys as $key) {
//            unset($array[$keys[$key]]); // Удаляем элемент
//          }
//        }
//      }
      foreach($array as $phone){
        echo $phone.'<br/>';
      }
      // echo count($array);
      die();
      Content::flushQueryCache();
      CompressContentImagesJob::dispatch(1)->onQueue('compressImages');
      dd(1);
      $orders = Order::where('status', 'has_error')->where('data_shipping->old_cdek_courier', '!=', null)->get();
      foreach($orders as $order){
        $data_shipping = $order->data_shipping;
        $data_shipping['cdek_courier'] = $data_shipping['old_cdek_courier'];
        $order->update([
            'data_shipping' => $data_shipping
        ]);
      }
      dd($orders);
      $orders = Order::query()
          ->where('data->total', '>=', 3500)
          ->where('created_at', '>=', '2023-12-15 10:00:00')
          ->where(function($query){
            $query->where('status', '!=', 'refund');
            $query->orWhere('status', null);
          })
          ->where('confirm', 1)
          ->get();
      // dd($orders);
      //$orders = Order::has('giftCoupons')->where('created_at', '>', '2023-11-13 10:00:00')->where('confirm', 0)->get();
      $i = 0;
      foreach($orders as $order){
//        $data_cart = $order->data_cart;
//        $data_cart[] = [
//            'id' => 58,
//            'qty' => 1,
//            'name' => 'Пенка для интимной гигиены (подарок)',
//            'image' => 'https://dlyakojida.ru/storage/photos/shares/products/foamintim/compressed/penka_ig-fotor-20231112132328-200.jpg',
//            'model' => 'foamintimgift',
//            'price' => 0
//        ];
//        $order->update([
//            'data_cart' => $data_cart
//        ]);
//        if($order->giftCoupons()->exists()){
//          $i;
//          $order->giftCoupons()->delete();
//          continue;
//        }
        if($order->giftCoupons()->count()<1){
          $i++;
          echo $order->id.'<br/>';
          //(new HappyCoupon())->setPrizeToOrder($order);
        }
//        if($order->giftCoupons()->where('prize_id', 22)->count()==3){
//          $i++;
////          $gift = $order->giftCoupons()->where('prize_id', 22)->where('data->position', null)->first();
////          if($gift){
////            $i++;
////            //dd($gift, $order);
////            $gift->delete();
////            (new HappyCoupon())->setPrizeToOrder($order);
////          }
//
//        }
      }
      dd($i, $orders);

      $order = Order::find(291587);
      for($i = 0;$i < 500;$i++){
        (new HappyCoupon())->setPrizeToOrder($order);
      }
      dd(1);
      $user = auth()->user();
      $user->subBonuses(100);
      dd(1);


      $order = Order::find(291560);

      (new HappyCoupon())->setPrizeToOrder($order);
      dd($order);

      // CreateZipJob::dispatch(storage_path('app/public/store_coupons'), storage_path('app/public/store_coupons.zip'))->onQueue('zip');

      dd(1);
      if (class_exists('ZipArchive')) {
        echo 'Расширение ZipArchive установлено.';
      } else {
        echo 'Расширение ZipArchive не установлено.';
      }
      dd(1);

      $user = User::find(2);
      $order = Order::find(40044);
      (new MailSender($user->email))->remindAboutReview($order);
      dd(1);
      $orders = Order::where('data_cart', 'like', '%_discounted%')->where('confirm', 0)->where('created_at', '>', '2023-12-01 00:00:00')->get();
      foreach($orders as $order){
        $cart = $order->data_cart;
        $qty = 0;
        foreach($cart as $item){
          $qty += $item['qty'];
        }
        if($qty < 3){
          echo $order->id.',';
          $order->setStatus('cancelled');
        }
      }
      $this->promotion();
      dd(123);
      Product::flushQueryCache();
      dd(1);
      //dd(urlToStoragePath('https://demo.lemousse.shop/storage/vouchers/3QEJ-338P-2590.png'));
      $order = Order::find(288245);
//      $user = $order->user;
//      (new MailSender($user->email))->confirmVouchersOrder($order, $user);
      (new OrderController)->finishOrder($order->user, $order);
      dd(1);
      $promo = Coupone::find(40601);
      dd($promo, $promo->partner);
      dd(1);
      $bx_order_ids = Order::query()
          ->doesntHave('tickets')
          ->where(function($query){
            $query->whereIn('status', ['boxberry_загружен реестр им', 'boxberry_заказ создан в личном кабинете', 'payment', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled']);
            $query->orWhere('status', null);
          })
          ->where('confirm', 1)
          ->where('data_shipping->ticket', null)
          ->whereIn('data_shipping->shipping-code', ['boxberry'])
          ->where(function ($query) {
            $query->where('data_shipping->boxberry->track', '!=', null);
          })->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
          ->orderBy('created_at', 'desc')
          ->pluck('id')->toArray();
      $boxberry_chunks = array_chunk($bx_order_ids, 50);
      dd($bx_order_ids);

      CheckOrdersStatusJob::dispatch(1)->onQueue('check_order_statuses');
      dd(1);
      $orders = Order::whereIn('data_shipping->shipping-code', ['boxberry'])
          ->where('status', '!=', null)
          ->whereNotIn('status', ['is_processing', 'is_waiting', ' was_sended_to_store', 'is_assembled', 'cancelled', 'refund', 'test', 'cdek_4', 'boxberry_выдано', 'is_ready', 'cdek_delivered', 'cdek_not_delivered', 'cdek_5', 'address_error', 'has_error'])
          ->where(function($query){
            $query->where('data_shipping->cdek->uuid', '!=', null);
            $query->orWhere('data_shipping->cdek_courier->uuid', '!=', null);
            $query->orWhere('data_shipping->boxberry->track', '!=', null);
          })
          ->where(function($query){
            $query->where('status_updated_at', '<', now()->subHours(12));
            $query->orWhere('status', 'was_processed');
          })
          ->whereIn('id', [289181, 289146, 289092, 289057, 289021, 288995])
          ->paginate(1, ['*'], 'page', 50);
      dd($orders);
      //CheckRobokassaPaymentsJob::dispatch(1)->onQueue('robokassa_payments');
//      $product = Product::select('id', 'name', 'sku', 'old_price', 'price', 'type_id', 'category_id', 'quantity', 'data_status', 'data_quantity', 'status', 'slug', 'style_page->cardImage->image->200 as image', 'product_options', 'product_id')->where('id', 1094)->first();
//      dd($product);
//      $products = Product::where('style_cards', null)->orWhere('style_cards', '[]]')->get();
//      foreach($products as $product){
//        $product->update([
//            'hidden' => true
//        ]);
//      }
      // dd(1);
      CheckOrdersStatusJob::dispatch(1)->onQueue('check_order_statuses');
      dd(1);


      foreach($arr as $arr_city){
        // cdek
//        $arr_exploded = explode(',', $arr_city);
//        $cdekCity = CdekCity::where('city', 'like', '%'.$arr_exploded[0].'%')->first();
//        if(!$cdekCity){
//          dd($arr_city);
//        }
//        echo $arr_city.' - '.$cdekCity->city.' '.$cdekCity->region.' '.$cdekCity->id.',';
//        $arr_exploded = explode(',', $arr_city);
//        $boxberryCity = BoxberryCity::where('Name', $arr_city)->first();
//        if(!$boxberryCity){
//          continue;
//        }
//        echo $boxberryCity->id.',';
        // cdek
        $arr_exploded = explode(' ', $arr_city);
        $key = $arr_exploded[0];
        if(mb_strtolower($arr_exploded[0]) == 'республика'){
          $key = $arr_exploded[1];
        }

        $cdekRegion = CdekRegion::where('region', 'like', '%'.$key.'%')->first();
        if(!$cdekRegion){
          continue;
        }
        echo $arr_city.' - '.$cdekRegion->name.' '.$cdekRegion->id.' ('.$key.'),<br/>';
        //echo $cdekRegion->id.', ';
      }

    dd(1);

      Product::flushQueryCache();
      $products = Product::select('id','sku', 'style_page')
          ->where('style_page->mainVideo->file', '!=', null)
          ->where('style_page->mainVideo->mp4', null)
          ->orderBy('id', 'desc')->get();
      // dd($products, $products->first()->style_page['mainVideo']);
      foreach($products as $product){
        $video = $product->style_page['mainVideo']['file'];
//        if(strpos($video, $product->sku) === false){
//          dd($product->id);
//        }else{
//          $style_page = $product->style_page;
//          $style_page['mainVideo']['mp4'] = $style_page['mainVideo']['file'];
//          $style_page['mainVideo']['file'] = preg_replace('/\.mp4/i', '.MOV', $style_page['mainVideo']['file']);
//          $product->update([
//              'style_page' => $style_page
//          ]);
//          continue;
//        }
        $path = parse_url($video, PHP_URL_PATH);

        // Удаляем префикс '/storage' из пути
        $open = str_replace('/storage', '', $path);
        // Получаем расширение файла
        $extension = pathinfo($open, PATHINFO_EXTENSION);
        // Если расширение .mov, заменяем его на .mp4
        if(mb_strtolower($extension) == 'mov') {
          $save = preg_replace('/\.mov$/i', '.mp4', $open);
        }else{
          dd($extension);
        }
        (new ProductController())->videoConversion($open, $save, $product->id);

//        if(mb_strtolower($extension) == 'mov') {
//          $save = preg_replace('/\.mov$/i', '.webm', $open);
//        }else{
//          dd($extension);
//        }
//        (new ProductController())->videoConversion($open, $save, $product->id);
        //VideoConversionJob::dispatch($open, $save, $product->id)->onQueue('video_convert');
        //dd($product->id, $products);
      }

      Product::flushQueryCache();
      dd(123);
      // тест сжатия видео
      $video = 'https://demo.lemousse.shop/storage/video/shares/products/immublend/IMG_5764.MOV';

      $path = parse_url($video, PHP_URL_PATH);

      // Удаляем префикс '/storage' из пути
      $open = str_replace('/storage', '', $path);
      // Получаем расширение файла
      $extension = pathinfo($open, PATHINFO_EXTENSION);
      // Если расширение .mov, заменяем его на .mp4
      if(mb_strtolower($extension) == 'mov') {
        $save = preg_replace('/\.mov$/i', '.mp4', $open);
      }else{
        dd($extension);
      }
      (new ProductController())->videoConversion($open, $save, 397);
      dd($save);
      // обновляем имена
      $users = User::select('id', 'name', 'first_name', 'last_name', 'middle_name')->where('first_name', null)->paginate(5000);
      foreach($users as $user){
        $order = $user->orders()->select('data->form->last_name as last_name', 'data->form->first_name as first_name', 'data->form->middle_name as middle_name')->where('data->form->full_name', $user->name)->first();
        $first_name = $user->getFirstName();
        $last_name = $user->getLastName();
        $middle_name = $user->getMiddleName();
        if($order){
          $first_name = $order->first_name;
          $last_name = $order->last_name;
          $middle_name = $order->middle_name;
        }
        $user->update([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'middle_name' => $middle_name,
        ]);
      }
      dd($users);
    }
    public function updateDB(){
//      $products = Product::all();
//      foreach($products as $product){
//        $type_id = 4;
//        if($product->category_id!=null){
//          $type_id = 3;
//        }
//        $product->update([
//            'category_id' => null,
//            'type_id' => $type_id
//        ]);
//      }
//      dd($products);
      $orders = Order::where('confirm', 0)->paginate(5000);
      foreach($orders as $order){
        $order->setStatus('cancelled');
//        if(!isset($order->data['statuses'])){
//          continue;
//        }
//        if($order->status_history()->count() == 0){
//          $statuses = $order->data['statuses'];
//          foreach($statuses as $status){
//            if(isset($this_status) && $this_status == $status['status']){
//              continue;
//            }
//            $this_status = $status['status'];
//
//            OrderStatusHistory::create([
//                'order_id' => $order->id,
//                'status' => $status['status'],
//                'created_at' => $status['date'],
//                'updated_at' => $status['date']
//            ]);
//          }
//        }
//
//        $order->update([
//            'status' => $order->data['this_status']['status'],
//            'status_updated_at' => $order->data['this_status']['last_updated'],
//        ]);
      }
      echo '<a href="https://lemousse.shop/admin/updateDB?page='.(request()->page+1).'">https://lemousse.shop/admin/updateDB?page='.(request()->page+1).'</a>';
      echo '<script>window.location = \'https://lemousse.shop/admin/updateDB?page='.(request()->page+1).'\'</script>';
      dd($orders);
    }

    public function updateQtyOptions(){
      $optionProducts = Product::dontCache()
          ->select('id', 'quantity', 'status', 'data_quantity', 'data_status', 'type_id', 'product_options', 'product_id')
          ->where('product_options->productSize', '!=', null)
          ->orWhere('type_id', 5)
          ->get();

      $parents = $optionProducts->where('type_id', 1);

      foreach($parents as $parent){
        if(!isset($parent->product_options['productSize'])||empty($parent->product_options['productSize'])) {
          continue;
        }
        $option_ids = $optionProducts->where('product_id', $parent->id)->pluck('id')->toArray();

        $option_products = $optionProducts->whereIn('id', $option_ids);
        $data = [
            'quantity' => 0,
            'status' => false,
            'data_quantity' => [],
            'data_status' => []
        ];
        foreach($option_products as $o_product){
          $data['quantity'] += $o_product->quantity;
          if($o_product->status) {
            $data['status'] = true;
          }
          foreach($o_product->data_quantity as $key => $o_product_data_q){
            if(!isset($data['data_quantity'][$key])){
              $data['data_quantity'][$key] = 0;
            }
            $data['data_quantity'][$key] += $o_product_data_q;
          }
          foreach($o_product->data_status as $key => $o_product_data_s){
            if(!isset($data['data_status'][$key])){
              $data['data_status'][$key] = false;
            }
            if($o_product_data_s){
              $data['data_status'][$key] = true;
            }
          }
        }
        $parent->update($data);
      }
    }

  private function prizesProbabilities(){
    $prizes = Prize::query()->select('id', 'total')->whereIn('id', [135,136,160,162,133,156,140,145,152,153,163,143,148,142,157,141,131,151])->get();
    $gifts = [];
    $hpDate = (new GiftCoupon)->getDate();
    foreach($prizes as $prize){
      $gave = $prize->giftCoupons()->where('created_at', '>=', $hpDate->format('Y-m-d H:i:s'))->count();
      $gifts[$prize->id] = $prize->total - $gave;
//      $prize->update([
//          'count' => $prize->total - $gave
//      ]);
    }

    function groupGifts($gifts) {
      $groupedGifts = [];

      foreach ($gifts as $id => $quantity) {
        $found = false;
        // Ищем, есть ли уже группа с таким количеством
        foreach ($groupedGifts as $key => $group) {
          if ($group['quantity'] === $quantity) {
            // Если нашли, добавляем ID к этой группе
            $groupedGifts[$key]['ids'][] = $id;
            $found = true;
            break;
          }
        }
        // Если такой группы нет, создаем новую
        if (!$found) {
          $groupedGifts[] = ['ids' => [$id], 'quantity' => $quantity];
        }
      }

      // Преобразуем массив, объединяя ID в строки
      $result = [];
      foreach ($groupedGifts as $group) {
        $key = implode(',', $group['ids']);
        $result[$key] = count($group['ids']) * $group['quantity'];
      }

      return $result;
    }
    function sortGifts(&$gifts) {
      uasort($gifts, function($a, $b) {
        return $a <=> $b;
      });
    }
    $groupedGifts = groupGifts($gifts);
    sortGifts($groupedGifts);
    $counted = 0;
    foreach($groupedGifts as $key => $total){
      if(!$total){
        continue;
      }
      $totalGifts = array_sum($groupedGifts);
      // echo "шанс подарка $key: ".round(($total + $counted)/$totalGifts*1000, 2).' при сумме '.$totalGifts.' - '.$total.'<br/>';
      echo '}elseif ($rand <= '.round(($total + $counted)/$totalGifts*1000, 0).' && Prize::query()->whereIn(\'id\', ['.$key.'])->where(\'count\', \'>\', 0)->where(\'active\', true)->exists()) {<br/>';
      echo '$win_item = array_merge($win_item, ['.$key.']);<br/>';
      $counted += $total;
    }
    dd($groupedGifts);
  }

  public function removeCdekWebhook($webhook_uuid){
    $client_id = 'yAJncWxYVbaczBAPiwxEXFCn7wbBjP85';
    $client_secret = 'ueA8pagyOFuVLDwScTPAQzAV88TLuCqA';
// Получение токена
    $authUrl = 'https://api.cdek.ru/v2/oauth/token?parameters';
    $credentials = [
        'grant_type' => 'client_credentials',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $authUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($credentials));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (!isset($responseData['access_token'])) {
      die('Не удалось получить токен доступа');
    }

    $access_token = $responseData['access_token'];

    $deleteUrl = "https://api.cdek.ru/v2/webhooks";
    $ch = curl_init($deleteUrl);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    dd($response);
// ID вебхука, который нужно удалить
    $webhookId = $webhook_uuid;

// Удаление вебхука
    $deleteUrl = "https://api.cdek.ru/v2/webhooks/$webhookId";
    $ch = curl_init($deleteUrl);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $access_token,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code == 204) {
      echo "Вебхук успешно удален";
    } else {
      echo "Ошибка при удалении вебхука: $response";
    }
  }
  public function telegramMailing(){
    $users = User::query()->whereHas('tgChats', function(Builder $builder){
      $builder->where('active', true);
    })
        ->where('is_subscribed_to_marketing', true)
//        ->whereDoesntHave('orders', function (Builder $builder){
//          $builder->where('confirm', 1);
//          $builder->where('created_at', '>', '2024-11-11 07:00:00');
////          $builder->where('created_at', '<', '2024-07-15 21:00:00');
//        })
//        ->whereHas('super_bonuses', function (Builder $builder){
//          $builder->where('amount', '>', 0);
//        })
        ->whereIn('id', [1,2]) // ,2
        ->pluck('id')->toArray();
//    dd($users);
    $mailing = MailingList::find(38);
    // Такие символы как '_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'
//      $text = "*ВПЕРВЫЕ\\!\\!\\!*\n\n";
//      $text .= "*Только 24ч*🔥"."\n\n";
//      $text .= "*💎Бонусное приключение: от Бриллиатовых суток к Золотым часам\\!💎*"."\n\n";
//      $text .= "https://lemousse\\.shop\n\n";
//      $text .= "_2 мая при оформлении заказа ВЕРНЕМ ВАМ 70% БОНУСАМИ,\n";
//      $text .= "которые можно потратить в определенный день и час_";

    $text = "🤫 *Твоё самое заветное желание может скоро исполниться*

Просто загадай мечту, которую хочешь осуществить сильнее всего, и поделись ей с нами

Неважно, большая или маленькая — ОНА МОЖЕТ БЫТЬ ЛЮБОЙ\!

И в Рождественскую ночь мы исполним любые желания 3\-х наших покупателей ✨

*Для участия:*

1️⃣ Соверши абсолютно любую покупку на сайте [Le Mousse](https://lemousse.shop/r/tlo) с 10:00 мск 25\.12\.24 до 15:00 мск 07\.01\.25

2️⃣ Заполни анкету в личном кабинете, указав в ней своё заветное желание

🎁 *7 января мы объявим имена 3\-х покупателей, чьи желания исполним\.* И, возможно, одним из них окажешься ты\!

У каждого есть шанс начать 2025 год с большого и настоящего чуда 👇

[❗️ЗАГАДАТЬ ЖЕЛАНИЕ — жми сюда](https://lemousse.shop/r/tlo)";

//    $text = "*ЕЩЁ ЕСТЬ ШАНС ПОЛУЧИТЬ БИЛЕТ С ПУТЕВКОЙ В ТУРЦИЮ*\n\n";
//
//    $text .= "Mini\\-сеты – это специально подобранные комбинации продуктов из мини\\-версий, в стильном боксе и\n";
//    $text .= "_по стоимости, выгоднее,_ чем при обычной покупке\\!👌"."\n\n\n";
//
//
//    $text .= "Это идеальный вариант на\n";
//    $text .= "под🎁арок близким"."\n";
//    $text .= "для путешествий\n";
//    $text .= "знакомства с продуктом по выгодной стоимости и с возможностью получить свой *«ЗОЛОТОЙ БИЛЕТ»*🎫"."\n\n";
//
//
//    $text .= "[ПЕРЕХОДИТЕ НА САЙТ](https://lemousse.shop/catalog/seti_mini_versiy)\n";
//    $text .= "и успейте воспользоваться предложением до 21:00 по Мск";

//    $text = "*ПОЛУЧИ СВОЙ «ЗОЛОТОЙ БИЛЕТ» С КРУТЫМИ ПОДАРКАМИ*\\!🤩"."\n\n";
//
//    $text .= "🔥Покупая mini\\-сеты\n";
//    $text .= "\\(||они еще и со СКИДКОЙ||\\),\n";
//    $text .= "у тебя есть шанс выиграть поездку в Турцию ✈️ и другие ценные подарки\\!🔥"."\n\n";
//
//    $text .= "[Смотреть предложение](https://lemousse.shop/catalog/seti_mini_versiy)\n\n";
//
//    $text .= "🚛Во время акции\n";
//    $text .= "ДОСТАВКА БЕСПЛАТНАЯ\n\n\n";
//
//
//    $text .= "_P\\.S\\._ у тебя есть ровно сутки, но  сеты могут закончиться быстрее\\!😉"."\n";
//    $text .= "\\_\\_\\_\\_\n";
//    $text .= "Le Mousse, с заботой о твоей коже❤️";
//    $text = "*Инструкция к красивой коже*🤫"."\n\n";
//
//    $text .= "✔️ Если хотите иметь здоровое и красиво свечение лица\n";
//    $text .= "✔️ Позволить себе ходить без макияжа/тона\n";
//    $text .= "✔️ Чувствовать легкость на лице без сухости\n\n";
//
//    $text .= "__ПЕРВОЕ на что нужно обратить внимание \\- на свое умывание\\!__\n\n";
//
//    $text .= "_Во многих средствах злощавые ПАВы,они нарушают микробиом_\n\n";
//
//    $text .= "_В итоге: сухость, преждевременное старение, дерматит, воспаления и т\\.д\\._\n\n";
//
//    $text .= "Гель для умывания от LE MOUSSE содержит *ценный комплекс ухаживающий компонентов, чтобы кожа работала ПРАВИЛЬНО✨*\n\n";
//
//    $text .= "Кожа меняется на глазах, если использовать комплексный подход  __\\(результат на фото выше\\)__\n\n";
//
//    $text .= "1 июля будет *АКЦИЯ 1\\+1\\=3*🎁"."\n\n";
//
//    $text .= "Вы соберете себе комплексный уход и *каждый третий продукт \\(самый дорогой\\) из корзины мы вам ДАРИМ*✨";
//    $text = "Девочки, у кого высыпания или раздражение на лице\\?\n\n";
//
//    $text .= "Сейчас жаркое лето и под воздействием высоких t \\- *наша нормальная микрофлора \\(микробиом\\) лица частично нарушается*\n\n";
//
//    $text .= "❗️Это и есть основная причина проблем на коже\n\n";
//
//    $text .= "__Что делать, чтобы она сияла здоровьем и была без «капризов»\\?__\n\n";
//
//    $text .= "В этом вам поможет наша *сыворотка для восстановления микробиома*\n\n";
//
//    $text .= "✔️ успокаивает раздраженную кожу за счёт пребиотиков, лизатов «хороших» бактерий\n\n";
//
//    $text .= "✔️ способствует запуску естественных процессов вашей кожи *за пару дней*\n\n";
//
//    $text .= "_Результат на фото выше_👆🏼\n";
//    $text .= "• 30 мл сыворотки хватает на 3 месяца\n\n";
//
//    $text .= "__*Как ее получить БЕСПЛАТНО?*__ \\(цена 6 700₽\\)\n\n";
//
//    $text .= "⚠️Ждите 1 июля \\(пн\\)\n\n";
//
//    $text .= "Будет АКЦИЯ 1\\+1\\=3🎁"."\n\n";
//
//    $text .= "Где самый дорогой продукт идет вам в подарок\\)";
//    $text = "__Как убрать \\- 1см в талии и бедрах за пару минут\\?__\n\n";
//
//    $text .= "\\*девочки, это актуально всем, кто не успел похудеть к лету😅\n\n";
//
//    $text .= "Вы попробуйте потереть себя скрабом от LE MOUSSE с ментоловым камнем 1\\-3 мин\n\n";
//
//    $text .= "Отеки и целлюлит уйдут моментально \\- подтверждено тысячами клиентами \\(на фото результат\\)👌🏻 \n\n";
//
//    $text .= "⚠️*1 июля «Клиентский день»*\n\n";
//
//    $text .= "1 \\+ 1 \\= 3🎁"."\n";
//    $text .= "Самый дорогой продукт в заказе вы забираете БЕСПЛАТНО\\!\n\n";
//
//        $text .= "_Например:\n";
//    $text .= "\\- Гель\\-йогурт для душа «Черная смородина» 2 900₽\n";
//    $text .= "\\- Extra Мусс для тела «Sweet dreams» 2 700₽_\n\n";
//
//    $text .= "🎁И охлаждающий скраб с ментоловым камнем идет вам в подарок *\\(экономия 2 990₽\\)*\n\n";
//
//    $text .= "То есть вы *БЕСПЛАТНО* можете избавиться от лишних см в талии и бедрах за пару минут\\)";
//    $text = "*ХОЧЕШЬ В ДУБАЙ\\?*\n\n";
//
//    $text .= "Только *24ч*🔥\nНаш клиентский день в LE MOUSSE\n*1 \\+ 1 \\= 3🎁 1000 подарков *✈️\n\n";
//
//    $text .= "https://lemousse\\.shop\n\n";
//
//    $text .= "Самый дорогой продукт в корзине в подарок, а так же возможность выиграть *Путевку в Дубай*, Apple IPhone, SPA боксы и еще *1000 крутых призов*\\!\n\n";
//
//    $text .= "_Акция распространяется на товары из одной категории💫_";
//      $text = "🔥*ЗОЛОТЫЕ ЧАСЫ настали*🔥"."\n\n";
//
//      $text .= "*Всего 2 часа🔥*"."\n\n";
//
//      $text .= "*https://lemousse\\.shop*\n\n";
//
//      $text .= "27 мая с 14:00 до 16:00 по мск при оформлении заказа вы можете оплатить до 100% бриллиантовыми бонусами\\!\n\n";
//      $text .= "_Успей, пока твои бонусы не сгорели\\._";
//
//      $text = "🔥 *МЕНЯЕМ ПРАВИЛА АКЦИИ 1 \\+ 1 \\= 3🎁 В ВАШУ ПОЛЬЗУ*🔥\n\n\n";
//
//
//      $text .= "https://lemousse\\.shop\n\n\n";
//
//
//      $text .= "Специальные условия майского «__Клиентского дня__»⬇️\n\n\n";
//
//
//      $text .= "📌 10 мая: ВПЕРВЫЕ\\! Вы можете приобрести продукты ИЗ ЛЮБОЙ категории\\! И самый дорогой продукт в корзине ИДЕТ В ПОДАРОК\\!\\!\\!\n";
//      $text .= "_\\(\\*В этот день не участвуют только мини версии\\)_\n\n\n";
//
//
//      $text .= "📌 11 мая:  УЧАСТВУЕТ ТОЛЬКО РАЗДЕЛ МИНИ ВЕРСИЙ\\!\n";
//      $text .= "Отличная возможность выгодно познакомиться с брендом или запастись удобной версией любимых продуктов для поездок и путешествий\\!";
//


//      $text .= "*Только 24ч*🔥"."\n\n";
//      $text .= "*💎Бонусное приключение: от Бриллиатовых суток к Золотым часам\\!💎*"."\n\n";
//      $text .= "https://lemousse\\.shop\n\n";
//      $text .= "_2 мая при оформлении заказа ВЕРНЕМ ВАМ 70% БОНУСАМИ,\n";
//      $text .= "которые можно потратить в определенный день и час_";
    $tgChats = TgChat::query()->with('user')
        ->whereIn('user_id', $users)
        ->where('active', true)
        ->chunk(500, function ($tgChats) use ($text, $mailing) {
          foreach($tgChats as $tgChat){
            $tgChat->user->mailing_list()->syncWithoutDetaching($mailing);
//            $tgChat->notify(new TelegramNotification($text, 'text_message', 'MarkdownV2'));
            $notification = new TelegramNotification($text, 'image_text_message', 'MarkdownV2', asset('telegram/photo_2024-12-25 08.30.55.jpeg'));
            $notification->delay(Carbon::parse('2024-12-19 14:50'));
            $tgChat->notify($notification);
          }
        });
  }

  public function prizesStat(){
    $hpDate = (new GiftCoupon)->getDate();
    $prizes = Prize::query()->whereIn('id', Prize::GENERAL)->get();
      foreach($prizes as $prize){
//        if($prize->total >= 100){
//          $prize->update([
//              'count' => 100
//          ]);
//        }else{
//          $prize->update([
//              'count' => 0
//          ]);
//        }
        $count = $prize->giftCoupons()->where('created_at', '>=', $hpDate->format('Y-m-d H:i:s'))->count();
        $prize->update([
            'count' => $prize->total - $count
        ]);
      }
      die();
    function countArraysWithNumber(array $data): int {
      return count(array_filter($data, fn($item) => is_array($item) && count($item) === 2 && in_array($item[0], [194, 195])));
    }
    $order = Order::find(355194);
    $orderCounter = [
        'order3500' => 0,
        'order7500' => 0,
        'order10500' => 0,
        'order15500' => 0,
    ];
    $giftCounter = [
        'order3500' => [
            '0' => 0,
            '1' => 0,
        ],
        'order7500' => [
            '1' => 0,
            '2' => 0,
            '3' => 0,
        ],
        'order10500' => [
            '2' => 0,
            '3' => 0,
            '4' => 0,
        ],
        'order15500' => [
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ],
    ];
    $totalGifts = 0;
    $totalEmpty = 0;
    for($i = 0;$i < 1000;$i++){
      $rand = mt_rand(1,100);
      if($rand <= 35){
        $total = 3500;
        $key = 'order3500';
      }elseif($rand <= 50){
        $total = 7500;
        $key = 'order7500';
      }elseif($rand <= 75){
        $total = 10500;
        $key = 'order10500';
      }else{
        $total = 15500;
        $key = 'order15500';
      }
      $orderCounter[$key]++;
      $order_data = $order->data;
      $order_data['total'] = $total;
      $order->update([
          'data' => $order_data
      ]);
      $prizes = (new HappyCoupon())->setPrizeToOrder($order);
      if(!is_array($prizes)){
        dd($prizes);
      }
      $emptyGifts = countArraysWithNumber($prizes);
      $gifts = count($prizes)-$emptyGifts;
      $giftCounter[$key]["$gifts"]++;
      $totalGifts += $gifts;
      $totalEmpty += $emptyGifts;
    }
    foreach(['order3500', 'order7500', 'order10500', 'order15500'] as $key){
      echo $key.'<br/><br/>';
      echo 'Всего заказов: '.$orderCounter[$key].'<br/>';
      foreach($giftCounter[$key] as $k => $v){
        echo 'Выпало '.$k.' подарков: '.$v.'<br/>';
      }
      echo '<br/>';
    }
    echo 'Всего пустых выпало '.$totalEmpty.'<br/>';
    echo 'Всего подарков выпало '.$totalGifts.'<br/>';

    die();
  }
  public function emailsByCity(Request $request){
    $city_name = mb_strtolower($request->input('city'));
    $cdek_city = CdekCity::query()->where(DB::raw('lower(`city`)'), $city_name)->pluck('id')->toArray();
    $cdek_pvzs = CdekPvz::query()->whereIn('city_id', $cdek_city)->pluck('code')->toArray();
    $boxberry_city = BoxberryCity::query()->where(DB::raw('lower(`Name`)'), $city_name)->pluck('id')->toArray();
    $boxberry_pvzs = BoxberryPvz::query()->whereIn('city_id', $boxberry_city)->pluck('code')->toArray();
    $orders = Order::query()->where(function($query) use ($cdek_pvzs, $boxberry_pvzs) {
      $query->whereIn('data_shipping->cdek-pvz-id', $cdek_pvzs);
      $query->orWhereIn('data_shipping->cdek-boxberry-id', $boxberry_pvzs);
    })
        ->distinct('user_id')
        ->where('confirm', 1)
        //->limit(900)
        ->get();
//    dd($cdek_city, $cdek_pvzs, $boxberry_city, $boxberry_pvzs);
    foreach($orders as $order){
      $user = $order->user;
      if(!$user->is_subscribed_to_marketing){
        continue;
      }
      echo $user->email.'<br/>';
    }
    die();
  }
  public function closed()
  {
    return response()->view('maintenanceBF1125end');
  }
}
