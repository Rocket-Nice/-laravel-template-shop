<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Memcached;

class SettingsController extends Controller
{
    public function index(){
      $settings = Setting::where('value', '!=', null)->paginate(100);
      $seo = [
          'title' => 'Системные настройки'
      ];
      return view('template.admin.settings.index', compact( 'seo', 'settings'));
    }
    public function mailSettings(){

    }
  public function info(){
    $jobs = DB::table('jobs')
        ->select('queue', DB::raw('COUNT(*) as total'),
            DB::raw('MAX(reserved_at IS NOT NULL) as active'))
        ->groupBy('queue')
        ->get();

    $memcached = new Memcached();
    $memcached->addServer('127.0.0.1', 11211);
    $stats = $memcached->getStats();

    $memcached_data = [];
    $memcached_data['usedMemory'] = $stats['127.0.0.1:11211']['bytes'] ?? 0;
    $memcached_data['totalMemory'] = $stats['127.0.0.1:11211']['limit_maxbytes'] ?? 0;
    $memcached_data['percentUsed'] = round(($memcached_data['usedMemory'] / $memcached_data['totalMemory']) * 100, 2);
    // Получаем все ключи
    $allKeys = $memcached->getAllKeys();
    // Фильтруем только ключи сессий Laravel
    $sessionKeys = array_filter($allKeys, function($key) {
      return strpos($key, 'leqc') === false && strpos($key, 'picture') === false && strpos($key, 'lemousse') !== false;
    });
    $sessionsCount = count($sessionKeys);

    $memcached_data['total_sessions'] = $sessionsCount;
    $memcached_data = collect($memcached_data);
    $seo = [
        'title' => 'Процессы в очереди'
    ];
    return view('template.admin.settings.info', compact('jobs', 'seo', 'memcached_data'));
  }
    public function settings(){
      $setting_keys = [
          'maintenanceStatus',
          'maintenanceNotification',
          'promo_1+1=3',
          'payment_test',
          'happyCoupon',
          'puzzlesStatus',
          'paymentMethod',
          'diamondPromo1',
          'diamondPromo2',
          'promo20',
          'promo30',
          'goldTicket'
      ];
      $settings = Setting::whereIn('key', $setting_keys)->get();
      $seo = [
          'title' => 'Общие настройки'
      ];
      return view('template.admin.settings.settings', compact( 'seo', 'settings'));
    }
    public function save(Request $request){
      $request->validate([
          'maintenanceStatus' => 'required',
          'maintenanceNotification' => 'required',
      ]);
      $request_array = $request->toArray();
      $settings_keys = array_keys($request_array);
      $settings = Setting::whereIn('key', $settings_keys)->get();
      foreach($settings as $setting){
        if($setting->value!=$request_array[$setting->key]){
          $old = $setting->toArray();
          $setting->update([
              'value' => $request_array[$setting->key]
          ]);
          $setting->addLog('Изменены настройки сайта', null, [
              'old' => $old,
              'new' => $setting->toArray()
          ]);
        }
      }
      Setting::flushQueryCache();
      return redirect()->route('admin.settings')->with([
          'success' => 'Настройки успешно изменены'
      ]);
    }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $seo = [
        'title' => 'Добавить настройку'
    ];
    return view('template.admin.settings.create', compact( 'seo'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $request->validate([
        'key' => 'required|max:255|unique:settings,key',
        'value' => 'required',
    ]);

    Setting::create([
        'key' => $request->key,
        'value' => $request->value,
    ]);
    Setting::flushQueryCache();
    return redirect()->route('admin.sytstem_settings.index')->with([
        'success' => 'Настройка успешно добавлена'
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Setting  $setting
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $setting = Setting::findOrFail($id);
    $seo = [
        'title' => 'Изменить настройку'
    ];

    return view('template.admin.settings.edit', compact('setting', 'seo'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Setting  $setting
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $setting = Setting::findOrFail($id);
    $request->validate([
        'key' => 'required|max:255',
        'value' => 'required',
    ]);
    if (Setting::where('key', $request->key)->where('id', '!=', $setting->id)->count()){
      return back()->withInput()->withErrors([
          'Данный код уже используется'
      ]);
    }
    $setting->update([
        'key' => $request->key,
        'value' => $request->value,
    ]);
    Setting::flushQueryCache();
    return redirect()->route('admin.sytstem_settings.index')->with([
        'success' => 'Настройка успешно изменена'
    ]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Setting  $setting
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $setting = Setting::findOrFail($id);
    $setting->delete();
    return redirect()->route('admin.sytstem_settings.index')->with([
        'success' => 'Настройка успешно удалена'
    ]);
  }
}
