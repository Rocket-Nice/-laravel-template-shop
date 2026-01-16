<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function user(Request $request)
  {
    $user = Auth::guard('web')->user();
    if(auth('web')->check()){
      return response()->json(['success' => true,
          'user' => [
              'id' => $user->id,
              'name' => $user->name,
              'email' => $user->email,
              'phone' => $user->phone,
          ]
      ], 200);
    } else {
      return response()->json(['success' => false, 'message' => 'User not found'], 401);
    }

  }
  public function login(Request $request)
  {
    $credentials = $request->validate([
        'email' => ['required', 'string', 'email:rfc,dns'],
        'password' => 'required|string',
    ]);

    if (Auth::guard('web')->attempt($credentials)) {
      $user = Auth::guard('web')->user();
      return response()->json(['success' => true,
          'user' => [
              'id' => $user->id,
              'name' => $user->name,
              'email' => $user->email,
              'phone' => $user->phone,
          ]
      ], 200);
    } else {
      return response()->json(['success' => false, 'message' => 'Invalid login credentials'], 401);
    }
  }
}
