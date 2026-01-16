<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\ShippingLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShippingLogController extends Controller
{
    public function index()
    {
      $log = ShippingLog::query();
      if(request()->type && request()->type != '{all}'){
        $log->where('code', request()->type);
      }
      if(request()->title){
        $log->where(DB::raw('lower(title)'), 'like', '%'.trim(request()->title).'%');
      }
      $log = $log->orderBy('created_at', 'desc')->paginate(100);
      $seo = [
          'title' => 'История отгрузки'
      ];
      return view('template.admin.shipping.log', compact('log', 'seo'));
    }
}
