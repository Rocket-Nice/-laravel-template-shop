<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AverageCheckExport;
use App\Exports\FinishedOrdersExport;
use App\Exports\NewUsersExport;
use App\Exports\OrderShippingExport;
use App\Exports\OrderStatuesExport;
use App\Exports\OrderTotalsExport;
use App\Exports\TopProductsExport;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Partner;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use SafeObject;

class ReportController extends Controller
{
    public function index(){
      $shipping_methods = ShippingMethod::query()
//          ->where('active', true)
          ->get();
      $products = Product::select('id', 'name', 'sku')
          ->where(function($query){
            $query->whereIn('type_id', [1,2,5,6,8,9]);
            $query->orWhere('id', 1185);
          })
          ->get();
      $referrers = null;
      if(auth()->user()->hasRole('admin')) {
        $referrers = Partner::select('id', 'name')->orderBy('created_at', 'desc')->get();
      }
      $seo = [
          'title' => 'Отчеты'
      ];
      return view('template.admin.reports.index', compact('seo', 'shipping_methods', 'products', 'referrers'));
    }


  /**
   * Вернёт [{bucket_start: '2025-07-17 14:30', total_sum: 1234.56}, …]
   *
   * @param  Carbon|string  $from   начало диапазона
   * @param  Carbon|string  $to     конец диапазона
   * @param  string         $unit   one of: minute, hour, day
   * @param  int            $step   шаг (N) — например 15 минут или 3 часа
   */
  function orderTotalsByPeriod(Request $request) {
    $from = $request->date_from ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from) : Carbon::now()->startofDay();
    $to = $request->date_until ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until) : Carbon::now()->endOfDay();
    $step = $request->step ?? 1;
    $unit = $request->unit ?? 'hour';
    $seconds = match ($unit) {
      'minute' => 60 * $step,
      'hour'   => 3600 * $step,
      'day'    => 86400 * $step,
      default  => throw new InvalidArgumentException('unit must be minute|hour|day'),
    };
    $filter_params = $request->toArray();
    $filter_params['date_from'] = $from->format('Y-m-d H:i');
    $filter_params['date_until'] = $to->format('Y-m-d H:i');
    $ordersSub = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('status', '!=', 'cancelled');
    if(!(is_array($request->status)&&in_array('refund', $request->status))){
      $ordersSub->where('status', '!=', 'refund');
    }
    $ordersSub->selectRaw(
        'FLOOR(UNIX_TIMESTAMP(orders.created_at) / ?) * ? AS bucket_ts,
            SUM(amount - JSON_EXTRACT(data_shipping, "$.price")) AS total_sum,
            COUNT(*) AS order_count',
        [$seconds, $seconds]
    )
        ->groupBy('bucket_ts');

    $itemsSub = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('status', '!=', 'cancelled');
    if(!(is_array($request->status)&&in_array('refund', $request->status))){
      $itemsSub->where('status', '!=', 'refund');
    }
        $itemsSub->join('order_items as oi', 'oi.order_id', '=', 'orders.id')
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(orders.created_at) / ?) * ? AS bucket_ts,
            SUM(oi.qty) AS item_count',  // исправлено
            [$seconds, $seconds]
        )
        ->groupBy('bucket_ts');

    $rawData = DB::query()
        ->fromSub($ordersSub, 'o')
        ->leftJoinSub($itemsSub, 'i', 'o.bucket_ts', '=', 'i.bucket_ts')
        ->select(
            'o.bucket_ts',
            'o.total_sum',
            'o.order_count',
            DB::raw('COALESCE(i.item_count, 0) as item_count')
        )
        ->orderBy('o.bucket_ts')
        ->get()
        ->keyBy('bucket_ts');

    // Создаем полный диапазон временных меток
    $startTs = floor($from->timestamp / $seconds) * $seconds;

    // Ограничиваем конечную временную метку текущим моментом
    $currentTs = floor(Carbon::now()->timestamp / $seconds) * $seconds;
    $endTs = min(floor($to->timestamp / $seconds) * $seconds, $currentTs);

    $fullRange = [];

    $runningTotal = 0;
    $runningOrderCount = 0;
    $runningItemCount = 0;
    $is_first = true;

    $totalPeriodSum = $rawData->sum('total_sum');
    $totalPeriodOrderCount = $rawData->sum('order_count');
    $totalPeriodItemCount = $rawData->sum('item_count');

    for ($ts = $startTs; $ts <= $endTs; $ts += $seconds) {
      if (isset($rawData[$ts])) {
        $runningTotal += $rawData[$ts]->total_sum;
        $runningOrderCount += $rawData[$ts]->order_count;
        $runningItemCount += $rawData[$ts]->item_count;
      }
      if($runningTotal == 0){
        continue;
      }
      $bucketStart = Carbon::createFromTimestamp($ts);
      $bucketEnd = Carbon::createFromTimestamp($ts + $seconds);

      $dateFormat = match ($unit) {
        'day'    => 'd M',
        'hour'   => 'd M H:i',
        'minute' => 'd M H:i',
        default  => 'Y-m-d H:i',
      };

// Если начало и конец в один день — выводим "7 июл 11:00–12:00"
      if ($unit === 'hour' || $unit === 'minute') {
        if ($bucketStart->isSameDay($bucketEnd)) {
          $label = $bucketStart->translatedFormat('d M H:i') . '–' . $bucketEnd->translatedFormat('H:i');
        } else {
          $label = $bucketStart->translatedFormat('d M H:i') . ' – ' . $bucketEnd->translatedFormat('d M H:i');
        }
      } elseif ($unit === 'day') {
        if ($bucketStart->isSameDay($bucketEnd->subSecond())) {
          $label = $bucketStart->translatedFormat('d M');
        } else {
          $label = $bucketStart->translatedFormat('d M') . ' – ' . $bucketEnd->subSecond()->translatedFormat('d M');
        }
      } else {
        $label = $bucketStart->translatedFormat($dateFormat) . ' – ' . $bucketEnd->translatedFormat($dateFormat);
      }

      $fullRange[] = (object)[
          'bucket_ts' => $ts,
          'bucket_start' => $bucketStart->toDateTimeString(),
          'bucket_end' => $bucketEnd->toDateTimeString(),
          'bucket_label' => $label,
          'total_sum' => $runningTotal,
          'total_sum_diff' => ($rawData[$ts]->total_sum ?? 0),
          'order_count' => $runningOrderCount,
          'order_count_diff' => ($rawData[$ts]->order_count ?? 0),
          'item_count' => $runningItemCount,
          'item_count_diff' => ($rawData[$ts]->item_count ?? 0),
          'total_period_sum' => $totalPeriodSum,
          'total_period_order_count' => $totalPeriodOrderCount,
          'total_period_item_count' => $totalPeriodItemCount,
      ];
      $is_first = false;
    }

    return response()->json($fullRange);
  }

  public function reportFinishedOrdersPage(){
    $shipping_methods = ShippingMethod::query()
//          ->where('active', true)
        ->get();
    $seo = [
        'title' => 'Завершенные заказы'
    ];
    return view('template.admin.reports.finished-orders', compact('seo', 'shipping_methods'));
  }


  public function reportFinishedOrdersData(Request $request) {
    $from = $request->date_from ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from) : Carbon::now()->startOfMonth();
    $to = $request->date_until ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until) : Carbon::now()->endOfDay();
    $step = $request->step ?? 1;
    $unit = $request->unit ?? 'day';
    $statuses = Status::query()->where('success', true)->pluck('key')->toArray();

    $seconds = match ($unit) {
      'minute' => 60 * $step,
      'hour'   => 3600 * $step,
      'day'    => 86400 * $step,
      default  => throw new InvalidArgumentException('unit must be minute|hour|day'),
    };

    $filter_params = $request->toArray();

    // Все подтвержденные заказы
    $totalOrdersSub = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('confirm', true)
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(created_at) / ?) * ? AS bucket_ts,
            COUNT(*) as total_count',
            [$seconds, $seconds]
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('bucket_ts');

    // Только заказы с нужными статусами
    $statusOrdersSub = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('confirm', true)
        ->whereIn('status', $statuses)
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(created_at) / ?) * ? AS bucket_ts,
            COUNT(*) as status_count',
            [$seconds, $seconds]
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('bucket_ts');

    $rawData = DB::query()
        ->fromSub($totalOrdersSub, 't')
        ->leftJoinSub($statusOrdersSub, 's', 't.bucket_ts', '=', 's.bucket_ts')
        ->selectRaw(
            't.bucket_ts,
            t.total_count,
            COALESCE(s.status_count, 0) as status_count'
        )
        ->orderBy('t.bucket_ts')
        ->get()
        ->keyBy('bucket_ts');

    $startTs = floor($from->timestamp / $seconds) * $seconds;
    $endTs = floor($to->timestamp / $seconds) * $seconds;

    $fullRange = [];
    $runningTotal = 0;
    $runningMatched = 0;
    $is_first = true;
    $total_orders = $rawData->sum('total_count');
    $total_finished = $rawData->sum('status_count');
    for ($ts = $startTs; $ts <= $endTs; $ts += $seconds) {
      $data = $rawData[$ts] ?? null;

      $total = $data->total_count ?? 0;
      $matched = $data->status_count ?? 0;

      $runningTotal += $total;
      $runningMatched += $matched;

      $bucketStart = Carbon::createFromTimestamp($ts);
      $bucketEnd = Carbon::createFromTimestamp($ts + $seconds);

      $label = match ($unit) {
        'hour', 'minute' => $bucketStart->isSameDay($bucketEnd)
            ? $bucketStart->translatedFormat('d M H:i') . '–' . $bucketEnd->translatedFormat('H:i')
            : $bucketStart->translatedFormat('d M H:i') . ' – ' . $bucketEnd->translatedFormat('d M H:i'),
        'day' => $bucketStart->isSameDay($bucketEnd->subSecond())
            ? $bucketStart->translatedFormat('d M')
            : $bucketStart->translatedFormat('d M') . ' – ' . $bucketEnd->subSecond()->translatedFormat('d M'),
        default => $bucketStart->translatedFormat('Y-m-d H:i') . ' – ' . $bucketEnd->translatedFormat('Y-m-d H:i'),
      };

      $fullRange[] = (object)[
          'bucket_ts' => $ts,
          'bucket_start' => $bucketStart->toDateTimeString(),
          'bucket_end' => $bucketEnd->toDateTimeString(),
          'bucket_label' => $label,
          'total_count' => $runningTotal,
          'status_count' => $runningMatched,
          'total_count_diff' => $is_first ? 0 : $total,
          'status_count_diff' => $is_first ? 0 : $matched,
          'total_orders' => $total_orders,
          'total_finished' => $total_finished,
      ];

      $is_first = false;
    }

    return response()->json($fullRange);
  }

  public function reportNewUsersPage(){
    $seo = [
        'title' => 'Новые клиенты'
    ];
    return view('template.admin.reports.new-users', compact('seo'));
  }

  public function reportNewUsersData(Request $request) {
    $from = $request->date_from ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from) : Carbon::now()->startOfMonth();
    $to = $request->date_until ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until) : Carbon::now()->endOfDay();
    $step = $request->step ?? 1;
    $unit = $request->unit ?? 'day';

    $seconds = match ($unit) {
      'minute' => 60 * $step,
      'hour'   => 3600 * $step,
      'day'    => 86400 * $step,
      default  => throw new InvalidArgumentException('unit must be minute|hour|day'),
    };

    // Subquery: получить user_id и дату первого подтвержденного заказа
    $firstOrders = Order::query()
        ->select('user_id', DB::raw('MIN(created_at) as first_order_at'))
        ->where('confirm', true)
        ->groupBy('user_id');

    // Фильтруем тех, чей первый заказ пришелся на нужный период
    $newUsers = DB::table(DB::raw("({$firstOrders->toSql()}) as first_orders"))
        ->mergeBindings($firstOrders->getQuery()) // передаем bindings
        ->whereBetween('first_order_at', [$from, $to])
        ->selectRaw('FLOOR(UNIX_TIMESTAMP(first_order_at) / ?) * ? AS bucket_ts, COUNT(*) as new_user_count', [$seconds, $seconds])
        ->groupBy('bucket_ts')
        ->orderBy('bucket_ts')
        ->get()
        ->keyBy('bucket_ts');

    // Строим диапазон и готовим финальную структуру
    $startTs = floor($from->timestamp / $seconds) * $seconds;
    $endTs = floor($to->timestamp / $seconds) * $seconds;

    $fullRange = [];
    $runningCount = 0;
    $is_first = true;
    $total_users = $newUsers->sum('new_user_count');
    for ($ts = $startTs; $ts <= $endTs; $ts += $seconds) {
      $count = $newUsers[$ts]->new_user_count ?? 0;
      $runningCount += $count;

      $bucketStart = Carbon::createFromTimestamp($ts);
      $bucketEnd = Carbon::createFromTimestamp($ts + $seconds);

      $label = match ($unit) {
        'hour', 'minute' => $bucketStart->isSameDay($bucketEnd)
            ? $bucketStart->translatedFormat('d M H:i') . '–' . $bucketEnd->translatedFormat('H:i')
            : $bucketStart->translatedFormat('d M H:i') . ' – ' . $bucketEnd->translatedFormat('d M H:i'),
        'day' => $bucketStart->isSameDay($bucketEnd->subSecond())
            ? $bucketStart->translatedFormat('d M')
            : $bucketStart->translatedFormat('d M') . ' – ' . $bucketEnd->subSecond()->translatedFormat('d M'),
        default => $bucketStart->translatedFormat('Y-m-d H:i') . ' – ' . $bucketEnd->translatedFormat('Y-m-d H:i'),
      };

      $fullRange[] = (object)[
          'bucket_ts' => $ts,
          'bucket_start' => $bucketStart->toDateTimeString(),
          'bucket_end' => $bucketEnd->toDateTimeString(),
          'bucket_label' => $label,
          'new_user_count' => $runningCount,
          'new_user_count_diff' => $is_first ? 0 : $count,
          'total_users' => $total_users,
      ];

      $is_first = false;
    }

    return response()->json($fullRange);
  }

  public function reportAverageCheckPage(){
    $seo = [
        'title' => 'Средняя стоимость заказов'
    ];
    return view('template.admin.reports.average-checks', compact('seo'));
  }

  public function reportAverageCheckData(Request $request) {
    $from = $request->date_from ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from) : Carbon::now()->startOfMonth();
    $to = $request->date_until ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until) : Carbon::now()->endOfDay();
    $step = $request->step ?? 1;
    $unit = $request->unit ?? 'day';

    $seconds = match ($unit) {
      'minute' => 60 * $step,
      'hour'   => 3600 * $step,
      'day'    => 86400 * $step,
      default  => throw new InvalidArgumentException('unit must be minute|hour|day'),
    };

    $filter_params = $request->toArray();

    $totalBasket = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('confirm', true)
        ->whereBetween('created_at', [$from, $to])
        ->selectRaw('COUNT(*) as count, SUM(JSON_EXTRACT(data, "$.total")) as sum')
        ->first();

    $totalShipping = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('confirm', true)
        ->whereRaw('JSON_EXTRACT(data_shipping, "$.price") > 0')
        ->whereBetween('created_at', [$from, $to])
        ->selectRaw('COUNT(*) as count, SUM(JSON_EXTRACT(data_shipping, "$.price")) as sum')
        ->first();

    $totalBasketAvg = $totalBasket->count > 0 ? $totalBasket->sum / $totalBasket->count : 0;
    $totalShippingAvg = $totalShipping->count > 0 ? $totalShipping->sum / $totalShipping->count : 0;
    // Сумма и средний чек по корзине
    $basketSub = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('confirm', true)
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(created_at) / ?) * ? AS bucket_ts,
            COUNT(*) as basket_count,
            SUM(JSON_EXTRACT(data, "$.total")) as basket_sum',
            [$seconds, $seconds]
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('bucket_ts');

    // Сумма и средний чек по доставке (> 0)
    $shippingSub = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->where('confirm', true)
        ->whereRaw('JSON_EXTRACT(data_shipping, "$.price") > 0')
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(created_at) / ?) * ? AS bucket_ts,
            COUNT(*) as shipping_count,
            SUM(JSON_EXTRACT(data_shipping, "$.price")) as shipping_sum',
            [$seconds, $seconds]
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('bucket_ts');

    $rawData = DB::query()
        ->fromSub($basketSub, 'b')
        ->leftJoinSub($shippingSub, 's', 'b.bucket_ts', '=', 's.bucket_ts')
        ->selectRaw(
            'b.bucket_ts,
            b.basket_count,
            b.basket_sum,
            s.shipping_count,
            s.shipping_sum'
        )
        ->orderBy('b.bucket_ts')
        ->get()
        ->keyBy('bucket_ts');

    $startTs = floor($from->timestamp / $seconds) * $seconds;
    $endTs = floor($to->timestamp / $seconds) * $seconds;

    $fullRange = [];
    $is_first = true;
    for ($ts = $startTs; $ts <= $endTs; $ts += $seconds) {
      $data = $rawData[$ts] ?? null;

      $basketAvg = $data && $data->basket_count > 0 ? $data->basket_sum / $data->basket_count : 0;
      $shippingAvg = $data && $data->shipping_count > 0 ? $data->shipping_sum / $data->shipping_count : 0;

      $bucketStart = Carbon::createFromTimestamp($ts);
      $bucketEnd = Carbon::createFromTimestamp($ts + $seconds);

      $label = match ($unit) {
        'hour', 'minute' => $bucketStart->isSameDay($bucketEnd)
            ? $bucketStart->translatedFormat('d M H:i') . '–' . $bucketEnd->translatedFormat('H:i')
            : $bucketStart->translatedFormat('d M H:i') . ' – ' . $bucketEnd->translatedFormat('d M H:i'),
        'day' => $bucketStart->isSameDay($bucketEnd->subSecond())
            ? $bucketStart->translatedFormat('d M')
            : $bucketStart->translatedFormat('d M') . ' – ' . $bucketEnd->subSecond()->translatedFormat('d M'),
        default => $bucketStart->translatedFormat('Y-m-d H:i') . ' – ' . $bucketEnd->translatedFormat('Y-m-d H:i'),
      };

      $fullRange[] = (object)[
          'bucket_ts' => $ts,
          'bucket_start' => $bucketStart->toDateTimeString(),
          'bucket_end' => $bucketEnd->toDateTimeString(),
          'bucket_label' => $label,
          'basket_avg' => round($basketAvg, 2),
          'shipping_avg' => round($shippingAvg, 2),
          'basket_count' => $data->basket_count ?? 0,
          'shipping_count' => $data->shipping_count ?? 0,
          'basket_sum' => $data->basket_sum ?? 0,
          'shipping_sum' => $data->shipping_sum ?? 0,
          'total_orders' => $totalBasket->count ?? 0,
          'total_basket' => round($totalBasketAvg, 2),
          'total_shipping' => round($totalShippingAvg, 2),
      ];

      $is_first = false;
    }
//    $fullRange[] = (object)[
//        'bucket_ts' => null,
//        'bucket_start' => $from->toDateTimeString(),
//        'bucket_end' => $to->toDateTimeString(),
//        'bucket_label' => 'Итого',
//        'basket_avg' => round($totalBasketAvg, 2),
//        'shipping_avg' => round($totalShippingAvg, 2),
//        'basket_count' => $totalBasket->count ?? 0,
//        'shipping_count' => $totalShipping->count ?? 0,
//        'basket_sum' => $totalBasket->sum ?? 0,
//        'shipping_sum' => $totalShipping->sum ?? 0,
//    ];
    return response()->json($fullRange);
  }

  public function reportStatusesPage(){
    $seo = [
        'title' => 'Статусы заказов'
    ];
    return view('template.admin.reports.statuses', compact('seo'));
  }

  public function reportStatusesData(Request $request){
    $filter_params = $request->toArray();
    $orderStatuses = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->get('total', 'status');
    $statuses = Status::query()->get();
    $res = collect([]);
    foreach($orderStatuses as $data){
      if(!$data->status){
        $status = $statuses->where('key', 'is_processing')->first();
      }else{
        $status = $statuses->where('key', mb_strtolower($data->status))->first();
      }

      $res[] = [
          'total' => $data->total,
          'status_key' => $data->status,
          'status' => $status?->name ?? $data->status,
          'color' => $status?->success ? 'bg-green-200' : ($status?->fail ? 'bg-red-200' : 'bg-gray-200'),
          'order' => $status?->order,
      ];
    }
    $res = $res->sortBy('order')->toArray();
    $res = array_values($res);
    return response()->json($res);
  }

  public function reportShippingPage(){
    $seo = [
        'title' => 'Способы доставки'
    ];
    return view('template.admin.reports.shipping', compact('seo'));
  }

  public function reportShippingData(Request $request){
    $filter_params = $request->toArray();
    $orderShipping = Order::query()
        ->filtered(new SafeObject($filter_params))
        ->select('data_shipping->shipping-code as shipping', DB::raw('count(*) as total'))
        ->where('data_shipping->shipping-code', '!=', null)
        ->groupBy('shipping')
        ->orderBy('total', 'desc')
        ->get('total', 'shipping');

    $shippingMethods = ShippingMethod::query()->get();
    $res = array();
    foreach($orderShipping as $data){
      $shipping = $shippingMethods->where('code', $data->shipping)->first();

      $res[] = [
          'total' => $data->total,
          'name' => $shipping?->name,
          'shipping' => $data->shipping,
      ];
    }
    return response()->json($res);
  }

  public function reportProductsPage(){
    $categories = Category::select('id', 'title', 'category_id')->get();
    $seo = [
        'title' => 'Топ-продукты'
    ];
    return view('template.admin.reports.products', compact('seo',  'categories'));
  }

  public function reportProductsData(Request $request){
    $from = $request->date_from ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from) : Carbon::now()->startOfMonth();
    $to = $request->date_until ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until) : Carbon::now()->endOfDay();

    $stats = DB::table('order_items')
        ->select('order_items.price', 'order_items.qty', )
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->whereBetween('orders.created_at', [$from->format('Y-m-d H:i'), $to->format('Y-m-d H:i')])
        ->where('orders.status', '!=', 'refund')
        ->where('orders.confirm', 1);
    if($request->category_id){
      $category_id = $request->category_id;
      $stats->where(function ($query) use ($category_id) {
        $query->where('products.category_id', $category_id)
            ->orWhereIn('products.id', function($q) use ($category_id) {
              $q->select('product_id')
                  ->from('category_product')
                  ->where('category_id', $category_id);
            });
      });
    }
    if($request->is_gift == 1){
      $stats->where('order_items.price', '>', 1);
    }elseif($request->is_gift == 2){
      $stats->where('order_items.price', '<=', 1);
    }

    $stats = $stats->groupBy('order_items.product_id', 'products.name', 'order_items.price')
        ->select(
            'products.name as product_name',
            DB::raw('SUM(order_items.qty) as total_qty'),
            DB::raw('SUM(order_items.qty * order_items.price) as total_amount')
        )
        ->orderByDesc('total_qty')
        ->get();
    return response()->json([
        'stats' => $stats,
        'total_qty' => $stats->sum('total_qty'),
        'total_amount' => $stats->sum('total_amount'),
    ]);
  }
  public function linksPage(){
    $seo = [
        'title' => 'Специальные ссылки'
    ];
    return view('template.admin.reports.links', compact('seo'));
  }

  public function linksData(Request $request){
    $startDate = $request->date_from ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from) : Carbon::now()->startOfMonth();
    $endDate = $request->date_until ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until) : Carbon::now()->endOfDay();

    $partners = Partner::query();
    if($request->link){
      $link = extractCode($request->link);
      $partners->where(DB::raw('lower(partners.slug)'), 'like', '%'.mb_strtolower($link).'%');
    }
    $partners = $partners->withCount([
        'visits as views_count' => function ($query) use ($startDate, $endDate) {
          $query->whereBetween('created_at', [$startDate, $endDate]);
        },
        'orders as orders_count' => function ($query) use ($startDate, $endDate) {
          $query->where('confirm', 1)
              ->where('status', '!=', 'cancelled')
              ->where('status', '!=', 'refund');
          $query->whereBetween('created_at', [$startDate, $endDate]);
        }
    ])
        ->with([
            'orders' => function ($query) use ($startDate, $endDate) {
              $query->where('confirm', 1)
                  ->where('status', '!=', 'cancelled')
                  ->where('status', '!=', 'refund')
                  ->whereBetween('created_at', [$startDate, $endDate]);
            }
        ])
        ->orderByDesc('views_count')
        ->get()
        ->map(function ($partner) {
          $totalSum = 0;
          if ($partner->relationLoaded('orders')) {
            // Возьмем коллекцию заказов из памяти (без нового запроса)
            $totalSum = $partner->getRelation('orders')->sum('amount');
          }

          return [
              'partner' => $partner->slug,
              'partner_info' => route('admin.reports.link', $partner->slug),
              'views_count' => $partner->views_count,
              'orders_count' => $partner->orders_count,
              'orders_total_sum' => $totalSum,
              'conversion' => $partner->views_count > 0 ? round($partner->orders_count / $partner->views_count * 100, 3) : 0,
          ];
        });
    return response()->json([
        'stats' => $partners,
        'total_views' => $partners->sum('views_count'),
        'total_orders' => $partners->sum('orders_count'),
        'total_orders_amount' => $partners->sum('orders_total_sum'),
    ]);
  }

  public function linkPage($link){
    $seo = [
        'title' => 'Статистика ссылки '.route('partner', $link)
    ];
    return view('template.admin.reports.link', compact('seo', 'link'));
  }

  public function linkData(Request $request, $link)
  {
    $partner = Partner::query()->where('slug', $link)->firstOrFail();
    $from = $request->date_from
        ? Carbon::createFromFormat('d.m.Y H:i', $request->date_from)
        : Carbon::now()->startOfMonth();
    $to = $request->date_until
        ? Carbon::createFromFormat('d.m.Y H:i', $request->date_until)
        : Carbon::now()->endOfDay();

    $step = $request->step ?? 1;
    $unit = $request->unit ?? 'day';

    $seconds = match ($unit) {
      'minute' => 60 * $step,
      'hour'   => 3600 * $step,
      'day'    => 86400 * $step,
      default  => throw new InvalidArgumentException('unit must be minute|hour|day'),
    };
    $filter_params = $request->toArray();
    $filter_params['date_from'] = $from->format('Y-m-d H:i');
    $filter_params['date_until'] = $to->format('Y-m-d H:i');
    // --- Визиты ---
    $visitsSub = $partner->visits()
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(date) / ?) * ? AS bucket_ts,
             COUNT(*) as views',
            [$seconds, $seconds]
        )
        ->whereBetween('created_at', [$from, $to])
        ->groupBy('bucket_ts');

    // --- Заказы (количество) ---
    $ordersSub = $partner->orders()
        ->filtered(new SafeObject($filter_params))
        ->where('status', '!=', 'cancelled')
        ->where('status', '!=', 'refund')
        ->where('confirm', 1)
        ->selectRaw(
            'FLOOR(UNIX_TIMESTAMP(created_at) / ?) * ? AS bucket_ts,
             COUNT(*) as orders_count,
             SUM(amount) as orders_sum',
//             SUM(amount - JSON_EXTRACT(data_shipping, "$.price")) as orders_sum',
            [$seconds, $seconds]
        )
        ->groupBy('bucket_ts');
    // объединяем данные
    $rawData = DB::query()
        ->fromSub($visitsSub, 'v')
        ->leftJoinSub($ordersSub, 'o', 'v.bucket_ts', '=', 'o.bucket_ts')
        ->select(
            'v.bucket_ts',
            'v.views',
            DB::raw('COALESCE(o.orders_count, 0) as orders_count'),
            DB::raw('COALESCE(o.orders_sum, 0) as orders_sum')
        )
        ->orderBy('v.bucket_ts')
        ->get()
        ->keyBy('bucket_ts');

    // диапазон
    $startTs = floor($from->timestamp / $seconds) * $seconds;
    $currentTs = floor(Carbon::now()->timestamp / $seconds) * $seconds;
    $endTs = min(floor($to->timestamp / $seconds) * $seconds, $currentTs);

    $result = [];

    $runningViews = 0;
    $runningOrders = 0;
    $runningSum = 0;

    $totalViews = $rawData->sum('views');
    $totalOrders = $rawData->sum('orders_count');
    $totalSum = $rawData->sum('orders_sum');

    for ($ts = $startTs; $ts <= $endTs; $ts += $seconds) {
      if (isset($rawData[$ts])) {
        $runningViews += $rawData[$ts]->views;
        $runningOrders += $rawData[$ts]->orders_count;
        $runningSum += $rawData[$ts]->orders_sum;
      }

      $bucketStart = Carbon::createFromTimestamp($ts)->addHours(3);
      $bucketEnd = Carbon::createFromTimestamp($ts + $seconds)->addHours(3);

      $dateFormat = match ($unit) {
        'day'    => 'd M',
        'hour'   => 'd M H:i',
        'minute' => 'd M H:i',
        default  => 'Y-m-d H:i',
      };

      if ($unit === 'hour' || $unit === 'minute') {
        if ($bucketStart->isSameDay($bucketEnd)) {
          $label = $bucketStart->translatedFormat('d M H:i') . '–' . $bucketEnd->translatedFormat('H:i');
        } else {
          $label = $bucketStart->translatedFormat('d M H:i') . ' – ' . $bucketEnd->translatedFormat('d M H:i');
        }
      } elseif ($unit === 'day') {
        if ($bucketStart->isSameDay($bucketEnd->subSecond())) {
          $label = $bucketStart->translatedFormat('d M');
        } else {
          $label = $bucketStart->translatedFormat('d M') . ' – ' . $bucketEnd->subSecond()->translatedFormat('d M');
        }
      } else {
        $label = $bucketStart->translatedFormat($dateFormat) . ' – ' . $bucketEnd->translatedFormat($dateFormat);
      }

      $result[] = (object)[
          'bucket_ts' => $ts,
          'bucket_label' => $label,
          'views_total' => $runningViews,
          'views_diff' => ($rawData[$ts]->views ?? 0),
          'orders_total' => $runningOrders,
          'orders_diff' => ($rawData[$ts]->orders_count ?? 0),
          'orders_sum_total' => $runningSum,
          'orders_sum_diff' => ($rawData[$ts]->orders_sum ?? 0),
          'total_period_views' => $totalViews,
          'total_period_orders' => $totalOrders,
          'total_period_sum' => $totalSum,
      ];
    }
    return response()->json($result);
  }


  public function export(Request $request)
  {
    // Получаем данные из метода напрямую
    if($request->report=='top-products'){
      $response = $this->reportProductsData($request);
      $data = json_decode($response->getContent());
      $name = 'report_top-products_'.now()->format('Y-m-d_H:i').'.xlsx';
      return Excel::download(new TopProductsExport($data), $name);
    }elseif($request->report=='order-shipping'){
      $response = $this->reportShippingData($request);
      $data = json_decode($response->getContent());
      $name = 'report_order-shipping_'.now()->format('Y-m-d_H:i').'.xlsx';
      return Excel::download(new OrderShippingExport($data), $name);
    }elseif($request->report=='order-statuses'){
      $response = $this->reportStatusesData($request);
      $data = json_decode($response->getContent());
      $name = 'report_order-statuses_'.now()->format('Y-m-d_H:i').'.xlsx';
      return Excel::download(new OrderStatuesExport($data), $name);
    }elseif($request->report=='new-clients'){
      $response = $this->reportNewUsersData($request);
      $data = json_decode($response->getContent());
      $name = 'report_new-clients_'.now()->format('Y-m-d_H:i').'.xlsx';
      return Excel::download(new NewUsersExport($data), $name);
    }elseif($request->report=='average-check'){
      $response = $this->reportAverageCheckData($request);
      $data = json_decode($response->getContent());
      $name = 'report_average-check_'.now()->format('Y-m-d_H:i').'.xlsx';
      return Excel::download(new AverageCheckExport($data), $name);
    }elseif($request->report=='finished-orders'){
      $response = $this->reportFinishedOrdersData($request);
      $data = json_decode($response->getContent());
      $name = 'report_finished-orders_'.now()->format('Y-m-d_H:i').'.xlsx';
      return Excel::download(new FinishedOrdersExport($data), $name);
    }
    $response = $this->orderTotalsByPeriod($request);
    $data = json_decode($response->getContent());
    $name = 'report_'.now()->format('Y-m-d_H:i').'.xlsx';
    return Excel::download(new OrderTotalsExport($data), $name);
  }
}
