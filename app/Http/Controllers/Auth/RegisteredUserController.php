<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'email' => ['required', 'confirmed', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed'],
            'birthday' => ['nullable'],
        ]);
        $email = str_replace(' ', '', strtolower(trim($request->email, ' ')));
        $name = $request->last_name . ' ' . $request->first_name;
        if (isset($request->middle_name) && !empty($request->middle_name)) {
          $name .= ' ' . $request->middle_name;
        }

        $phone = preg_replace("/[^,.0-9]/", '', $request->phone);
        $phone = preg_replace('/^(89|79|9)/', '+79', $phone);
        if ($phone[0] == '9') {
          $phone = '+7' . $phone;
        }
        $user_phone = User::where('phone', '=', $phone)->exists();
        if ($user_phone) {
          return back()->withInput()->withErrors([
              'message' => 'Данный телефон уже используется'
          ]);
        }
      $birthday = request()->birthday ? Carbon::createFromFormat('d.m.Y', request()->birthday)->format('Y-m-d') : null;

      $user = User::create([
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'email' => $email,
            'phone' => $phone,
            'birthday' => $birthday,
            'password' => Hash::make($request->password),
            'is_new' => true
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
