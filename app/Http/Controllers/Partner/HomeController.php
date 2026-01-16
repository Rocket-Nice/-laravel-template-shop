<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Referer;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
      $user = auth()->user();
      if(auth()->user()->hasRole('admin') && request()->get('u')){
        $user = User::findOrFail(request()->get('u'));
      }
      if (!$user->partner()->exists()){
        abort('403', 'У вас нет доступа к данной странице');
      }
      $partner = $user->partner;
      $settings = Setting::where('key', 'partnersDate')->first();
      if($settings){
        $date_start = $settings->value;
      }else{
        $date_start = now()->format('Y-m-d');
      }

      $orders = $partner->orders()
          ->select('id', 'data->form->email as email', 'amount', 'data->total as total', 'created_at')
          ->where('confirm', 1)
          ->where('created_at', '>', $date_start);
      $sum = $orders->sum('amount');
      $total = $orders->sum('data->total');
      $orders = $orders->orderBy('id', 'desc')->paginate(50);
      return view('template.partner.index', compact('orders', 'partner', 'sum', 'total'));
    }
}
