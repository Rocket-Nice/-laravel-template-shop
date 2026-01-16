<?php

namespace App\Http\Controllers\Admin\HappyCoupon;

use App\Http\Controllers\Controller;
use App\Models\Coupone;
use App\Models\Partner;
use App\Models\Setting;
use App\Models\User;
use App\Models\Referer;
use Carbon\Carbon;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $partners = Partner::query()
            ->select('partners.id', 'partners.name', 'partners.slug', 'users.email', 'partners.user_id', 'coupones.code as coupon_code', 'coupones.amount', 'coupones.type')
            ->leftJoin('users', 'users.id', '=', 'partners.user_id')
            ->leftJoin('coupones', 'coupones.id', '=', 'partners.coupone_id');
      $partners = $partners->paginate(50);
      $settings = Setting::whereIn('key', ['partnersDate'])->get();
        $seo = [
            'title' => 'Партнеры'
        ];
        return view('template.admin.happy_coupon.partners.index', compact('seo', 'partners', 'settings'));
    }

    public function settings(Request $request)
    {
      $request->validate([
          'partnersDate' => 'required'
      ]);
      $request_array = $request->toArray();
      $settings_keys = array_keys($request_array);
      $settings = Setting::whereIn('key', $settings_keys)->get();
      foreach($settings as $setting){
        if($setting->value!=$request_array[$setting->key]){
          $old = $setting->toArray();
          if($setting->key == 'partnersDate'){
            $date = Carbon::createFromFormat('d.m.Y', $request_array[$setting->key]);
            if ($date) {
              $request_array[$setting->key] = $date->format('Y-m-d');
            }
          }
          $setting->update([
              'value' => $request_array[$setting->key]
          ]);
          $setting->addLog('Изменены настройки партнерского кабинета', null, [
              'old' => $old,
              'new' => $setting->toArray()
          ]);
        }
      }
      Setting::flushQueryCache();
      return redirect()->route('admin.partners.index')->with([
          'success' => 'Настройки успешно изменены'
      ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $seo = [
          'title' => 'Добавить партнера'
      ];
      return view('template.admin.happy_coupon.partners.create', compact('seo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $request->validate([
          'name' => 'required|string',
          'description' => 'nullable|string',
          'redirect' => 'nullable|url',
          'email' => 'required|email',
          'code' => 'required|unique:coupones,code',
      ]);
      $exists = Partner::query()->where('slug', $request->code)->exists();
      if($exists){
        return back()->withInput()->withErrors([
            'Данный код уже используется'
        ]);
      }
      $email = strtolower(trim($request->email));
      $patner_user = User::where('email', '=', $email)->first();
      $user_options = [];

      $mailmessage = (new MailMessage)
          ->subject(config('app.name').' – партнерский кабинет')
          ->greeting('Здравствуйте!')
          ->line('Для вас создан партнерский кабинет на сайте lemousse.shop<br/>')
          ->line('Ссылка для входа: https://dlyakojida.ru/partner')
          ->line('Логин: '.$email);
      if ($patner_user) {
        if ($patner_user->partner()->exists()){
          return redirect()->route('admin.partners.index')->withErrors([
              'Пользователь не создан, так как у этого пользователя уже подключен другой партнерский кабинет'
          ]);
        }
        $user_id = $patner_user->id;
        $mailmessage->line('Парол остался пержний, если не помните свой пароль, нажмите "восстановить пароль" на странице входа.');
      }else{
        $rand_num = implode('', array_map(function () {
          return mt_rand(0, 9);
        }, range(1, 11)));
        $password = Str::random(8);
        $patner_user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $email,
            'phone' => $rand_num,
            'email' => $email,
            'password' => Hash::make($password),
            'options' => $user_options
        ]);

        $user_id = $patner_user->id;
        $mailmessage->line('Пароль: '.$password);
      }
      $role = Role::where('name', 'Партнер')->first();
      $patner_user->assignRole($role);
     //  Notification::route('mail', $email)->notify(new MailNotification($mailmessage));

      if($request->get('promocode')) {
        $promocode_params = [
            'code' => $request->get('code'),
            'type' => 2,
            'amount' => 5,
            'count' => 99999,
            'available_until' => now()->addDays(7)
        ];
        $coupone = Coupone::create($promocode_params);
      }

      $code = $request->get('code');
      $partner_data = [
          'user_id' => $user_id,
          'coupone_id' => $coupone->id ?? null,
          'name' => $request->get('name'),
          'description' => $request->get('description'),
          'data' => null,
          'redirect' => $request->get('redirect'),
          'slug' => $code,
      ];
      Partner::create($partner_data);
      return redirect()->route('admin.partners.index')->with([
          'success' => 'Партнер успешно добавлен'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partner $partner)
    {
      // сбор статистики
        if(request()->date_from){
          $date_start = Carbon::createFromFormat('d.m.Y', request()->date_from); // ->format('Y-m-d H:i')
          $date_until = $date_start->copy()->addWeeks(2)->endOfDay();
        }else{
          $date_start = now()->copy()->subWeeks(2);
          $date_until = now()->endOfDay();
        }
      $pageViews = $partner->visits()->select(
          DB::raw('DATE(date) as date'),
          DB::raw('count(*) as views')
      )
          ->whereBetween('date', [$date_start->format('Y-m-d H:i:s'), $date_until->format('Y-m-d H:i:s')])
          ->groupBy(DB::raw('DATE(date)'))
          ->get()
          ->pluck('views', 'date')
          ->all();
      $orders = $partner->orders()->select(
          DB::raw('DATE(created_at) as date'),
          DB::raw('count(*) as views')
      )
          ->whereBetween('created_at', [$date_start->format('Y-m-d H:i:s'), $date_until->format('Y-m-d H:i:s')])
          ->groupBy(DB::raw('DATE(created_at)'))
          ->get()
          ->pluck('views', 'date')
          ->all();
      $paidOrders = $partner->orders()->select(
          DB::raw('DATE(created_at) as date'),
          DB::raw('count(*) as views')
      )
          ->where('confirm', 1)
          ->whereBetween('created_at', [$date_start->format('Y-m-d H:i:s'), $date_until->format('Y-m-d H:i:s')])
          ->groupBy(DB::raw('DATE(created_at)'))
          ->get()
          ->pluck('views', 'date')
          ->all();

          $dateRange = collect();
          while ($date_start->lte($date_until)) {
            $dateRange->push($date_start->copy());
            $date_start->addDay();
          }

        $viewsByDate = $dateRange->mapWithKeys(function ($date) use ($pageViews) {
          return [$date->format('d.m.Y') => $pageViews[$date->format('Y-m-d')] ?? 0];
        })->all();
        $ordersByDate = $dateRange->mapWithKeys(function ($date) use ($orders) {
          return [$date->format('d.m.Y') => $orders[$date->format('Y-m-d')] ?? 0];
        })->all();
        $paidOrdersByDate = $dateRange->mapWithKeys(function ($date) use ($paidOrders) {
          return [$date->format('d.m.Y') => $paidOrders[$date->format('Y-m-d')] ?? 0];
        })->all();
        // генерация qr кода
        if(!isset($partner->data['qrcode'])){
          $link = route('partner', $partner->slug);
          $img_path = public_path().'/img/partner-qr';
          $img_name = '/qr_code-'.$partner->slug.'.png';
          if (!file_exists($img_path.$img_name)) {
            $writer = new PngWriter();

            // Create QR code
            $qrCode = QrCode::create($link)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
                ->setSize(300)
                ->setMargin(10)
                ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));

            $result = $writer->write($qrCode);
            if (!file_exists($img_path)) {
              mkdir($img_path, 0777, true);
            }
            $result->saveToFile($img_path.$img_name);
            $linkQrUri = $result->getDataUri();
            $partner_data = $partner->data;
            $partner_data['qrcode'] = '/img/partner-qr'.$img_name;

            $partner->update([
                'data' => $partner_data
            ]);
          }
        }
        $seo = [
            'title' => 'Редактировать партнера: '.$partner->name
        ];
        return view('template.admin.happy_coupon.partners.edit', compact('seo', 'partner', 'viewsByDate', 'ordersByDate', 'paidOrdersByDate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
      $request->validate([
          'name' => 'required|string',
          'description' => 'nullable|string',
          'redirect' => 'nullable|url',
          'email' => 'required|email',
          'code' => 'required',
      ]);

      $email = strtolower(trim($request->email));
      $patner_user = User::where('email', '=', $email)->first();
      $user_options = [];

      if ($patner_user) {
        if ($patner_user->partner()->exists()&&$patner_user->partner->id!=$partner->id){
          return redirect()->route('admin.partners.index')->withErrors([
              'Пользователь не создан, так как у этого пользователя уже подключен другой партнерский кабинет'
          ]);
        }
        $user_id = $patner_user->id;
      }else{
        $rand_num = implode('', array_map(function () {
          return mt_rand(0, 9);
        }, range(1, 11)));
        $password = Str::random(8);
        $patner_user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $email,
            'phone' => $rand_num,
            'email' => $email,
            'password' => Hash::make($password),
            'options' => $user_options
        ]);

        $user_id = $patner_user->id;
      }
      $role = Role::where('name', 'Партнер')->first();
      $patner_user->assignRole($role);

      if($request->get('promocode')) {
        $coupone = Coupone::where('code', $request->get('code'))->first();
        if(!$coupone){
          $promocode_params = [
              'code' => $request->get('code'),
              'type' => 2,
              'amount' => 5,
              'count' => 99999,
              'available_until' => now()->addDays(7)
          ];
          $coupone = Coupone::create($promocode_params);
        }
      }


      $code = $request->get('code');
      $partner_data = [
          'user_id' => $user_id,
          'coupone_id' => $coupone->id ?? null,
          'name' => $request->get('name'),
          'description' => $request->get('description'),
          'data' => null,
          'redirect' => $request->get('redirect'),
          'slug' => $code,
      ];
      $partner->update($partner_data);
      return redirect()->route('admin.partners.index')->with([
          'success' => 'Партнер успешно обновлен'
      ]);
    }

  public function statistic(Partner $partner)
  {
    // сбор статистики
    if(request()->date_from){
      $date_start = Carbon::createFromFormat('d.m.Y', request()->date_from);
    }else{
      $date_start = now()->copy()->subWeeks(2);
    }
    if(request()->date_to){
      $date_until = Carbon::createFromFormat('d.m.Y', request()->date_to);
    }else{
      $date_until = now()->endOfDay();
    }
    $pageViews = $partner->visits()->select(
        DB::raw('DATE(date) as date'),
        DB::raw('count(*) as views')
    )
        ->whereBetween('date', [$date_start->format('Y-m-d 00:00:00'), $date_until->format('Y-m-d 23:59:59')])
        ->groupBy(DB::raw('DATE(date)'))
        ->get()
        ->pluck('views', 'date')
        ->all();
    $orders = $partner->orders()->select(
        DB::raw('DATE(created_at) as date'),
        DB::raw('count(*) as views')
    )
        ->where('status', '!=', 'cancelled')
        ->where('status', '!=', 'refund')
        ->where('confirm', 1)
        ->whereBetween('created_at', [$date_start->format('Y-m-d 00:00:00'), $date_until->format('Y-m-d 23:59:59')])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get()
        ->pluck('views', 'date')
        ->all();

    $ordersSum = $partner->orders()->select(
        DB::raw('DATE(created_at) as date'),
        DB::raw("SUM(JSON_UNQUOTE(JSON_EXTRACT(data, '$.total'))) as total_sum")
    )
        ->where('status', '!=', 'cancelled')
        ->where('status', '!=', 'refund')
        ->where('confirm', 1)
        ->whereBetween('created_at', [$date_start->format('Y-m-d 00:00:00'), $date_until->format('Y-m-d 23:59:59')])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->get()
        ->pluck('total_sum', 'date')
        ->all();

    $dateRange = collect();
    while ($date_start->lte($date_until)) {
      $dateRange->push($date_start->copy());
      $date_start->addDay();
    }

    $viewsByDate = $dateRange->mapWithKeys(function ($date) use ($pageViews) {
      return [$date->format('d.m.Y') => $pageViews[$date->format('Y-m-d')] ?? 0];
    })->all();
    $ordersByDate = $dateRange->mapWithKeys(function ($date) use ($orders) {
      return [$date->format('d.m.Y') => $orders[$date->format('Y-m-d')] ?? 0];
    })->all();
    $ordersSumByDate = $dateRange->mapWithKeys(function ($date) use ($ordersSum) {
      return [$date->format('d.m.Y') => $ordersSum[$date->format('Y-m-d')] ?? 0];
    })->all();

    $seo = [
        'title' => 'Статистика партнера: '.$partner->name
    ];
    return view('template.admin.happy_coupon.partners.statistic', compact('seo', 'partner', 'viewsByDate', 'ordersByDate', 'ordersSumByDate'));
  }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
