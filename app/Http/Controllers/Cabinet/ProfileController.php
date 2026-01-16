<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
  public function index(){
    $user = auth()->user();
    $seo = [
        'title' => 'Данные пользователя'
    ];
    return view('template.cabinet.profile.index', compact('user', 'seo'));
  }

  public function update(Request $request){
    $validate = [];
    $validate['first_name'] = ['required', 'string', 'max:30'];
    $validate['middle_name'] = ['nullable', 'string', 'max:30'];
    $validate['last_name'] = ['required', 'string', 'max:30'];
    $validate['email'] = ['required', 'string', 'email:rfc,dns', 'max:255'];
    $validate['phone'] = ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'];
    $validate['birthday'] = ['nullable'];

    $user = auth()->user();
    $request->validate($validate);
    if (User::where('email', $request->email)->where('id', '!=', $user->id)->count()){
      return back()->withInput()->withErrors([
          'Данный email уже используется другим пользователем'
      ]);
    }

    $email = strtolower($request->email);
    $phone = preg_replace("/[^,.0-9]/", '', $request->phone);
    if (isset($request->country) && $request->country == 0) {
      $phone = preg_replace('/^(89|79|9)/', '+79', $request->phone);
      if ($phone[0] == '9') {
        $phone = '+7' . $phone;
      }
    }
    $name = $request->last_name . ' ' . $request->first_name;
    if (isset($request->middle_name) && !empty($request->middle_name)) {
      $name .= ' ' . $request->middle_name;
    }
    $birthday = request()->birthday ? Carbon::createFromFormat('d.m.Y', request()->birthday)->format('Y-m-d') : null;
    if($user->birthday){
      $birthday = $user->birthday->format('Y-m-d');
    }
    $user_params = [
        'name' => $name,
        'last_name' => $request->last_name,
        'first_name' => $request->first_name,
        'middle_name' => $request->middle_name,
        'email' => $email,
        'birthday' => $birthday,
        'phone' => $phone
    ];
    if(!empty($request->password)){
      $user_params['password'] = Hash::make($request->password);
    }
    $user->update($user_params);
    return redirect()->route('cabinet.profile.index')->with([
        'success' => 'Ваши данные успешно изменены'
    ]);
  }
}
