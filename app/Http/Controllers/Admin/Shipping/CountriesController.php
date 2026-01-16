<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Pickup;
use App\Models\ShippingMethod;
use CodersStudio\SmsRu\Notifications\SmsRu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountriesController extends Controller
{
    public function index(Request $request)
    {
        $countries = Country::orderBy('name');
        if($request->keyword){
          $keyword = trim($request->keyword);
          $keyword = mb_strtolower($keyword);

          if (is_numeric($keyword)) {
            $countries->where('id', $keyword);
          }else{
            $countries->where(DB::raw('lower(name)'), 'like', '%'.$keyword.'%');
          }
        }
        if($request->delivery){
          $countries->whereJsonContains('options->status', $request->delivery);
        }
        $countries = $countries->paginate(50);
        $shipping_methods = ShippingMethod::select('code', 'name')->get();
      $seo = [
          'title' => 'Страны'
      ];
        return view('template.admin.shipping.countries.index', compact('countries', 'shipping_methods'));
    }

    public function updateCounties(Request $request)
    {
      $request->validate([
          'country_ids' => ['required', 'array'],
          'action' => ['required', 'string'],
      ]);
      $country_ids = $request->country_ids;
      $action = explode('|', $request->action);

      $countries = Country::select()->whereIn('id', $country_ids);

      $message_success = '';
      if ($action[0] == 'toggle') { // устанавливаем статус
        $countries = $countries->get();
        $delivery_codes = explode(',', $action[1]);
        $status = $action[2];
        foreach ($countries as $country) {
          $options = [];
          if ($country->options){
            $options = $country->options;
          }
          foreach($delivery_codes as $code){
            if (isset($options['status'])&&array_search($code, $options['status']) !== false){
              unset($options['status'][array_search($code, $options['status'])]);
            }
            if ($status) {
              if ($code == 'pochta' && !isset($options['pochta_code'])) {
                continue;
              }
              if ($code == 'bxb' && !isset($options['bxb'])) {
                continue;
              }
              $options['status'][] = $code;
            }
          }
          $country->update([
              'options' => $options
          ]);
        }
        $message_success = 'Статус успешно изменен';
      }
      Country::flushQueryCache();
      return back();
    }
}
