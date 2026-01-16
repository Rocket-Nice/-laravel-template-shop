<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupone;
use App\Models\Partner;
use App\Models\Referer;
use App\Models\User;
use App\Notifications\MailNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      dd(123);
        $partners = Partner::query()
            ->select('partners.id', 'partners.name', 'partners.slug', 'users.email', 'partners.user_id', 'coupones.code as coupon_code', 'coupones.amount', 'coupones.type')
            ->leftJoin('users', 'users.id', '=', 'partners.user_id')
            ->leftJoin('coupones', 'coupones.id', '=', 'partners.coupone_id');
      $partners = $partners->paginate(50);
        $seo = [
            'title' => 'Партнеры'
        ];
        return view('template.admin.partners.index', compact('seo', 'partners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $seo = [
          'title' => 'Добавить партнера'
      ];
      return view('template.admin.partners.create', compact('seo'));
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
            'amount' => 10,
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
          'redirect' => null,
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
        $seo = [
            'title' => 'Редактировать партнера: '.$partner->name
        ];
        return view('template.admin.partners.edit', compact('seo', 'partner'));
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
              'amount' => 10,
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
          'redirect' => null,
          'slug' => $code,
      ];
      $partner->update($partner_data);
      return redirect()->route('admin.partners.index')->with([
          'success' => 'Партнер успешно обновлен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
