<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Shipping\CdekController;
use App\Http\Controllers\Shipping\PochtaController;
use App\Models\BoxberryPvz;
use App\Models\BoxberryRegion;
use App\Models\CdekCity;
use App\Models\CdekCourierCity;
use App\Models\CdekPvz;
use App\Models\CdekRegion;
use App\Models\Setting;
use App\Models\ShippingMethod;
use App\Models\X5PostCity;
use App\Models\X5PostPvz;
use App\Models\X5PostRegion;
use App\Services\RussianPost;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
  private $cart;

  public function __construct(){
    $this->cart = Cart::instance('cart');
    $this->middleware('throttle:100000,1')->only('verify', 'resend');
  }

  public function getCdekRegions(Request $request){
    $country = [
        1 => 'RU',
        14 => 'AM',
        2 => 'BY',
        46 => 'KZ',
        57 => 'KG'
    ];
    $request->validate([
        'country' => ['numeric', 'required']
    ]);
    $regions = CdekRegion::select('id', 'region_code', 'region')->where('country_code', $country[$request->country])
        ->has('pvzs');
    if($request->has('method') == 'nt'){
      $regions->where('region_code', CdekRegion::NEW_TERRITORY_REGION_CODE);
    }else{
      $regions->where('region_code', '!=', CdekRegion::NEW_TERRITORY_REGION_CODE);
    }
    $regions = $regions->orderBy('region')->get();

    return $regions->toArray();
  }
  public function getCdekCities(Request $request){
    $request->validate([
        'region' => 'required|numeric'
    ]);

    $cities = CdekCity::where('region_id', $request->region)
//        ->whereNotIn('id', [393369,393259,394445,393487,393420,393690,393688,393373,393372,393809,393385,393689,393289,394455,394763,394083,394686,394753,394119,394101,393579,394702,394465,393576,394081,394733,393341,394082,393342,394705,393550,394444,393270,393577,393580,393868,393581,394729,394492,393466,393733,393703,393250,394715,393886,393554,393555,393313,393805,393885,394139,393553,393353,393254,393578,393557,394479,393340,393257,393831,393492,393352,393503,393792,393556,393558,394557])
        ->select('id', 'code', 'city')->whereHas('pvzs', function ($query){
          $query->where('is_active', true);
        })->orderBy('city', 'asc')->get();

    return $cities->toArray();
  }
  public function getCdekCourierCities(Request $request){
    $request->validate([
        'region' => 'required|numeric'
    ]);

//      $cities = City::where('cities.options->cdek->region_code', $request->region)
//          ->orderBy('cities.options->cdek->city', 'asc')
//          ->get();
    // $cities = $region->cities()->orderBy('name', 'asc')->get();

    $cities = CdekCourierCity::where('region_id', $request->region)->where('active', true)->select('id', 'code', 'city', 'sub_region')->orderBy('city', 'asc')->get();

    return $cities->toArray();
  }
  public function getCdekPvz(Request $request, $pvz_id = null){
    $pvzs = CdekPvz::where('region_id', '=', $request->region)
        ->where('type', '!=', 'POSTAMAT')
        ->where('is_active', true);
    if($request->city){
      $pvzs->where('city_id', '=', $request->city);
    }
    $pvzs = $pvzs->get();
    $i = 0;
    $map = array();
    $method = 'cdek';
    if($request->get('method') == 'nt'){
      $method = 'nt';
    }
    $points = '<div class="mb-6"><select id="'.$method.'-pvz" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black"><option disabled selected value="">Выбрать...</option>';
    foreach($pvzs as $pvz){
      $pvz_data = json_decode($pvz->pvz_data, true);
      $map['data']['features'][$i]['type'] = 'Feature';
      $map['data']['features'][$i]['id'] = $pvz['code'];
      $map['data']['features'][$i]['geometry'] = [
          'type' => 'Point',
          'coordinates' => [$pvz['latitude'], $pvz['longitude']]
      ];
      $balloonContentBody = '<div style="padding: 15px; text-align: center;"><a class="btn btn-fw btn-primary btn-block ll_set_point" data-pvz-code="' . $pvz['code'] . '" data-pvz-address="' . $pvz['address'] . '"  data-city-code="' . $pvz['city_code'] . '" data-pvz-city="' . $pvz['city'] . '">Выбрать</a></div>';
      $balloonContentBody .= $pvz['address_full'];

      if (!isset($pvz_data['phones']) || !empty($pvz_data['phones'])) {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz_data['phones'][0]['number'];
      }
      if (!isset($pvz['note']) || $pvz['note'] == '') {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz['note'];
      }
      if (!isset($pvz['address_comment']) || $pvz['address_comment'] == '') {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz['address_comment'];
      }

      $map['data']['features'][$i]['properties'] = [
          'hintContent' => $pvz['address'],
          'balloonContentHeader' => $pvz['address'],
          'balloonContentBody' => $balloonContentBody
      ];
      $map['data']['features'][$i]['options']['preset'] = 'islands#darkGreenDotIcon';
      $map['data']['features'][$i]['params']['code'] = $pvz['type'];

      if ((isset($deliveryVariantId) && $deliveryVariantId == $pvz['code'])||!isset($deliveryVariantId)) {
        if(!isset($deliveryVariantId)) {
          $deliveryVariantId = $pvz['code'];
        }
        $selected = '';
        // $title = $pvz['objectTypeName'].' - '.$pvz['name'];
        $title = $pvz['address'];
      } else {
        $selected = '';
      }
      if ($pvz_id && $pvz_id == $pvz['code']){
        $selected = ' selected';
      }
      $points .= '<option id="'.$pvz['code'].'" value="'.$pvz['code'].'"'.$selected.' data-address="'.$pvz['address'].'" data-city="'.$pvz['city_code'].'" data-city-name="'.$pvz['city'].'">'.$pvz['address'].'</option>';
      $i++;
    }
    $points .= '</select></div>';

    $map['data']['type'] = 'FeatureCollection';
    $map['delivery']['PVZ'] = [
        'code' => 'PVZ',
        'content' =>  'ПВЗ',
        'title' => 'ПВЗ'
    ];
//    $map['delivery']['POSTAMAT'] = [
//        'code' => 'POSTAMAT',
//        'content' =>  'Постамат',
//        'title' => 'Постамат'
//    ];
    $map['controls'] = ['zoomControl'];
    // $this->debug($map);
    $map_script = '<div class="mb-4"><input type="hidden" id="data_map_cdek" value="'.e(json_encode($map, JSON_UNESCAPED_UNICODE)).'"/><button type="button" id="findPvzOnMap_cdek" class="block w-full border border-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black">Выбрать на карте</button></div>';
//    if (!$pvz_id) {
//      $map_script = '<div id="mapContainer"></div>';
//    }else{
//      $map_script = '<div id="mapContainer"></div>';
//    }

    return [
        'html' => $points.$map_script
    ];
  }
  public function getCdekCourierRegions(Request $request){
    $country = [
        1 => 'RU',
        14 => 'AM',
        2 => 'BY',
        46 => 'KZ',
        57 => 'KG'
    ];
    $request->validate([
        'country' => ['numeric', 'required']
    ]);
    $regions = CdekRegion::select('id', 'region_code', 'region')->where('country_code', $country[$request->country])
        // ->whereNotIn('id', [65, 37, 38, 17, 34, 31, 79, 56, 75, 55,87, 136, 54, 3, 53, 140, 9, 45, 10, 63, 30, 20, 4, 137, 61, 67, 23, 76, 8, 68, 19, 13, 41, 73, 11, 2, 43, 28, 60, 24, 80, 27, 34, 15, 58, 57, 52, 69, 44, 77, 47, 71, 72, 52, 46, 74, 18, 51, 5, 29, 40, 62, 26, 25, 81, 141, 64, 16, 70, 39, 49,])
        ->orderBy('region')->get();

    return $regions->toArray();
  }

  public function getBoxberryRegions(){
    $regions = BoxberryRegion::orderBy('name')->get();

    return $regions->toArray();
  }

  public function getBoxberryCities(Request $request){
    $request->validate([
        'region' => 'required|numeric|exists:boxberry_regions,id'
    ]);
    $region = BoxberryRegion::findOrFail($request->region);
    $cities = $region->cities()
        // ->whereNotIn('id', [44,595,428,6,278,410,666,293,329,145,683,385,451,689,283,470,291,682,418,575,168,491,396,265,503,298,362,603,574,594,652,153,334,602,3,395,493,444,311,231,657,375,214,369,269,386,450,417,669,275,276,151,279,653,607,169,662,282,215,210,322,416,401,480,246,245,414,468,166,422,159,360,496,382,663,326,374,167,489,132,261,160,338,156,357,150,363,411,54,267,327,555,440,146,262,498,654,257,364,263,588,256,259,373,485,455,264,272,273,691,456,181,384,260,147,420,152,158,182,702,437,476,477,258,143,177,178,290,365,484,179,439])
        ->orderBy('name', 'asc')->get();

    return $cities->toArray();
  }

  public function getBoxberryPvz(Request $request, $code = null){
    $request->validate([
        'region' => 'required|numeric|exists:boxberry_regions,id',
        'city' => 'required|numeric|exists:boxberry_cities,id'
    ]);

    $pvzs = BoxberryPvz::where('region_id', '=', $request->region)
        ->where('city_id', '=', $request->city)
        ->orderBy('address_reduce', 'DESC')
        ->get();
    $i = 0;
    $map = array();
    $points = '<div class="mb-6"><select id="yandex-pvz" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black"><option disabled selected value="">Выбрать...</option>';
    foreach($pvzs as $pvz){
      $pvz = $pvz->toArray();

      $map['data']['features'][$i]['type'] = 'Feature';
      $map['data']['features'][$i]['id'] = $pvz['code'];

      $gemoetry = explode(',', $pvz['GPS']);

      $map['data']['features'][$i]['geometry'] = [
          'type' => 'Point',
          'coordinates' => [$gemoetry[0], $gemoetry[1]]
      ];
      $balloonContentBody = '<div style="padding: 15px; text-align: center;"><a class="btn btn-primary ll_set_point" data-pvz-code="' . $pvz['code'] . '" data-pvz-address="' . $pvz['address'] . '"">Выбрать</a></div>';
      $balloonContentBody .= $pvz['address'];

      if (!isset($pvz['phone']) || $pvz['phone'] == '') {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz['phone'];
      }
      if (!isset($pvz['trip_description']) || $pvz['trip_description'] == '') {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz['trip_description'];
      }
//      if (!isset($pvz['howToGet']) || $pvz['howToGet'] == '') {
//        $balloonContentBody .= '';
//      }else{
//        $balloonContentBody .= '<br>' . $pvz['howToGet'];
//      }

      $map['data']['features'][$i]['properties'] = [
          'hintContent' => $pvz['address_reduce'],
          'balloonContentHeader' => $pvz['address_reduce'],
          'balloonContentBody' => $balloonContentBody
      ];
      $map['data']['features'][$i]['options']['preset'] = 'islands#darkGreenDotIcon';
      $map['data']['features'][$i]['params']['code'] = 'PVZ';

      if ((isset($deliveryVariantId) && $deliveryVariantId == $pvz['code'])||!isset($deliveryVariantId)) {
        if(!isset($deliveryVariantId)) {
          $deliveryVariantId = $pvz['code'];
        }
        $selected = '';
        // $title = $pvz['objectTypeName'].' - '.$pvz['name'];
        $title = $pvz['address_reduce'];
      } else {
        $selected = '';
      }
      if ($code && $code == $pvz['code']){
        $selected = ' selected';
      }
      $points .= '<option id="'.$pvz['code'].'" value="'.$pvz['code'].'"'.$selected.' data-address="'.$pvz['address'].'">'.$pvz['address_reduce'].'</option>';
      $i++;
    }
    $points .= '</select></div>';

    $map['data']['type'] = 'FeatureCollection';
    $map['delivery']['PVZ'] = [
        'code' => 'PVZ',
        'content' =>  'ПВЗ',
        'title' => 'ПВЗ'
    ];
//    $map['delivery']['POSTAMAT'] = [
//        'code' => 'POSTAMAT',
//        'content' =>  'Постамат',
//        'title' => 'Постамат'
//    ];
    $map['controls'] = ['zoomControl'];
    // $this->debug($map);
    $map_script = '<div class="mb-4"><input type="hidden" id="data_map_yandex" value="'.e(json_encode($map, JSON_UNESCAPED_UNICODE)).'"/><button type="button" id="findPvzOnMap_yandex" class="block w-full border border-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black">Выбрать на карте</button></div>';
//    if (!$code) {
//      $map_script = '<div id="mapContainer"></div><script>map_init(' . json_encode($map) . ');map_show_modal(\'\');</script>';
//    }else{
//      $map_script = '<div id="mapContainer"></div><script>setTimeout(function(){map_init(' . json_encode($map) . ');map_show_modal(\'\');},1000);</script>';
//    }

    return [
        'html' => $points.$map_script
    ];
  }

  public function getX5PostRegions(){
    $regions = X5PostRegion::query()
        ->whereHas('pvzs', function ($query) {
          $query
              ->where('is_active', true)
              ->where('rate', 'not like', '[]');
        })
        ->orderBy('name')->get();

    return $regions->toArray();
  }

  public function getX5PostCities(Request $request){
    $request->validate([
        'region' => 'required|numeric|exists:x5_post_regions,id'
    ]);
//    $region = X5PostRegion::findOrFail($request->region);
    $cities = X5PostCity::query()
        ->where('region_id', $request->region)
        ->whereHas('pvzs', function ($query) {
          $query
              ->where('is_active', true)
              ->where('rate', 'not like', '[]');
        })
        ->orderBy('name', 'asc')->get();

    return $cities->toArray();
  }

  public function getX5PostPvz(Request $request, $code = null){
    $request->validate([
        'region' => 'required|numeric|exists:x5_post_regions,id',
        'city' => 'required|numeric|exists:x5_post_cities,id'
    ]);

    $pvzs = X5PostPvz::where('region_id', '=', $request->region)
        ->where('city_id', '=', $request->city)
        ->orderBy('shortAddress', 'DESC')
        ->orderBy('fullAddress', 'DESC')
        ->where('is_active', true)
        ->where('rate', 'not like', '[]')
        ->get();
    $i = 0;
    $map = array();
    $points = '<div class="mb-4"><select id="x5post-pvz" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black"><option disabled selected value="">Выберите ПВЗ</option>';
    foreach($pvzs as $pvz){
      $pvz = $pvz->toArray();

      $map['data']['features'][$i]['type'] = 'Feature';
      $map['data']['features'][$i]['id'] = $pvz['mdmCode'];

      $gemoetry = [$pvz['address_lat'], $pvz['address_lng']];

      $map['data']['features'][$i]['geometry'] = [
          'type' => 'Point',
          'coordinates' => [$gemoetry[0], $gemoetry[1]]
      ];
      $balloonContentBody = '<div style="padding: 15px; text-align: center;"><bottom class="border border-customBrown inline-flex items-center justify-center text-customBrown rounded-full bg-transparent placeholder-customBrown text-sm h-11 px-4 leading-none focus:ring-0 focus:border-black ll_set_point" data-pvz-code="' . $pvz['mdmCode'] . '" data-pvz-address="' . $pvz['fullAddress'] . '"">Выбрать</bottom></div>';
      $balloonContentBody .= $pvz['fullAddress'];

      if (!isset($pvz['phone']) || $pvz['phone'] == '') {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz['phone'];
      }
      if (!isset($pvz['additional']) || $pvz['additional'] == '') {
        $balloonContentBody .= '';
      }else{
        $balloonContentBody .= '<br>' . $pvz['additional'];
      }
      $map['data']['features'][$i]['properties'] = [
          'hintContent' => $pvz['shortAddress'] ?? $pvz['fullAddress'],
          'balloonContentHeader' => $pvz['shortAddress'] ?? $pvz['fullAddress'],
          'balloonContentBody' => $balloonContentBody
      ];
      $map['data']['features'][$i]['options']['preset'] = 'islands#darkGreenDotIcon';
      $map['data']['features'][$i]['params']['code'] = 'PVZ';

      if ((isset($deliveryVariantId) && $deliveryVariantId == $pvz['mdmCode'])||!isset($deliveryVariantId)) {
        if(!isset($deliveryVariantId)) {
          $deliveryVariantId = $pvz['mdmCode'];
        }
        $selected = '';
        // $title = $pvz['objectTypeName'].' - '.$pvz['name'];
        $title = $pvz['shortAddress'] ?? $pvz['fullAddress'];
      } else {
        $selected = '';
      }
      if ($code && $code == $pvz['mdmCode']){
        $selected = ' selected';
      }
      $points .= '<option id="'.$pvz['mdmCode'].'" value="'.$pvz['mdmCode'].'"'.$selected.' data-address="'.$pvz['fullAddress'].'">'.($pvz['shortAddress'] ?? $pvz['fullAddress']).'</option>';
      $i++;
    }
    $points .= '</select></div>';

    $map['data']['type'] = 'FeatureCollection';
    $map['delivery']['PVZ'] = [
        'code' => 'PVZ',
        'content' =>  'ПВЗ',
        'title' => 'ПВЗ'
    ];
    $map['controls'] = ['zoomControl'];
    $map_script = '<div class="mb-4"><input type="hidden" id="data_map_x5post" value="'.e(json_encode($map, JSON_UNESCAPED_UNICODE)).'"/><button type="button" id="findPvzOnMap_x5post" class="block w-full border-0 border-b border-b-myGray bg-transparent placeholder-myGray m-text-body d-text-body py-1.5 px-3 focus:ring-0 focus:border-b-black">Выбрать на карте</button></div>';

    return [
        'html' => $points.$map_script
    ];
  }
  public function calculateCdek(Request $request) {
    $cdek = new CdekController();
    $address = '';
    if ($request->shipping&&$request->shipping=='cdek_courier') {
      $price_data = $cdek->calcCourier($request->city);
      $add_price = ShippingMethod::select('add_price')->where('code', 'cdek_courier')->first();
      if (isset($price_data['shippingPrice'])){
        $price = $price_data['shippingPrice'] + ($add_price->add_price ?? 0);
        $price_experss = isset($price_data['shippingPriceExpress']) ? (int)$price_data['shippingPriceExpress'] + ($add_price->add_price ?? 0) : null;
      }else{
        return $price_data;
      }
    }else{
      $request->validate([
          'code' => 'required|exists:cdek_pvzs,code'
      ]);
      $pvz = CdekPvz::select('city_code', 'region', 'city', 'address')->where('code', $request->code)->first();
      $address = $pvz->region.', '.$pvz->city.', '.$pvz->address;
      $city_code = $pvz->city_code;
      if($city_code == 101010101){
        $city_code = 2206787;
      }
      $price_data = $cdek->calc($city_code);

      $add_price = ShippingMethod::select('add_price')->where('code', 'cdek')->first();
      if (isset($price_data['shippingPrice'])){
        $price = $price_data['shippingPrice'] + ($add_price->add_price ?? 0);
        $price_experss = isset($price_data['shippingPriceExpress']) ? $price_data['shippingPriceExpress'] + ($add_price->add_price ?? 0) : null;
      }else{
        return $price_data;
      }
    }

    $cart = $this->cart;

    $total = 0;
    foreach($cart->content() as $item){
      $total += $item->price * $item->qty;
    }
    $price = round($price);
    $result = [
        'shippingPrice' => $price ?? null,
        'shippingPriceExpress' => $price_experss ?? null,
        'address' => $address,
        'total' => $total,
    ];
    $cart = Cart::instance('cart');


    if(!getSettings('promo_1+1=3')){
      if($request->shipping!='cdek_courier'){
        $result['shippingPrice2'] = $result['shippingPrice'];
        $result['shippingPrice'] = 0;
      }
    }else{
      $count = $cart->content()->sum(function ($item) {
        return !$item->options->gift ? $item->qty : 0;
      });

      if($request->shipping!='cdek_courier' && $count>=3 && !Setting::whereIn('key', ['promo_1+1=3', 'happyCoupon', 'promo20', 'diamondPromo1', 'diamondPromo2'])->where('value', 1)->first()){
        $result['shippingPrice2'] = $result['shippingPrice'];
        $result['shippingPrice'] = 0;
      }
    }

    session(['shippingPrice'=>$price]);
    return $result;
  }

  public function calculateBoxberry(Request $request) {
    $request->validate([
        'code' => 'required|exists:boxberry_pvzs,code'
    ]);
    $yandex = new \WildTuna\BoxberrySdk\Client(300, 'https://api.boxberry.ru/json.php');
    $yandex->setToken('main', config('services.boxberry.apikey')); // Заносим токен BB и присваиваем ему ключ main
    $yandex->setCurrentToken('main');

//      $places = $this->ozon->deliveryFromPlaces([]);
//      dd($places);
    $cart = Cart::instance('cart');
    $pvz = BoxberryPvz::select('code', 'address')->where('code', $request->code)->first();

    try {
      $calcParams = new \WildTuna\BoxberrySdk\Entity\CalculateParams();
      $calcParams->setWeight(1000);
      $calcParams->setPvz($pvz->code);
      $calcParams->setAmount($cart->total(0, '.', ''));

      $calcParams->setTargetStart('cec5d25c-fdf4-479d-baad-f88054025d6a');
      
      $result = $yandex->calcTariff($calcParams);

      /*
         WildTuna\BoxberrySdk\Entity\TariffInfo Object
         (
             [price:WildTuna\BoxberrySdk\Entity\TariffInfo:private] => 176.25
             [price_base:WildTuna\BoxberrySdk\Entity\TariffInfo:private] => 168
             [price_service:WildTuna\BoxberrySdk\Entity\TariffInfo:private] => 8.253
             [delivery_period:WildTuna\BoxberrySdk\Entity\TariffInfo:private] => 2
         )
       */
    }

    catch (\WildTuna\BoxberrySdk\Exception\BoxBerryException $e) {
      $error = $e->getMessage();
      // dd($e);
      // Обработка ошибки вызова API BB
      // $e->getMessage(); текст ошибки
      // $e->getCode(); http код ответа сервиса BB
      // $e->getRawResponse(); // ответ сервера BB как есть (http request body)
    }

    catch (\Exception $e) {
      $error = $e->getMessage();
      /// dd($e);
      // Обработка исключения
    }
    if (isset($error)&&$error){
      Log::debug($error);
      $result = [
          'error' => 'При расчете стоимости произошла ошибка'
      ];
      return $result;
    }
    $add_price = ShippingMethod::select('add_price')->where('code', 'boxberry')->first();
    $price = $result->getPrice() + ($add_price->add_price ?? 0);
    $total = 0;
    foreach($cart->content() as $item){
      $total += $item->price * $item->qty;
    }
    $result = [
        'shippingPrice' => round($price),
        'shippingFormatPrice' => formatPrice(round($price)),
        'address' => $pvz->address,
        'total' => $total
    ];
    $cart = Cart::instance('cart');


    if(!getSettings('promo_1+1=3')){
      if($request->shipping!='cdek_courier'){
        $result['shippingPrice2'] = $result['shippingPrice'];
        $result['shippingPrice'] = 0;
      }
    }else{
      $count = $cart->content()->sum(function ($item) {
        return !$item->options->gift ? $item->qty : 0;
      });

      if($request->shipping!='cdek_courier' && $count>=3 && !Setting::whereIn('key', ['promo_1+1=3', 'happyCoupon', 'promo20', 'diamondPromo1', 'diamondPromo2'])->where('value', 1)->first()){
        $result['shippingPrice2'] = $result['shippingPrice'];
        $result['shippingPrice'] = 0;
      }
    }

    return $result;
  }

  public function calculateX5Post(Request $request) {
    $request->validate([
        'code' => 'required|exists:x5_post_pvzs,mdmCode'
    ]);
//    Log::debug('123');
    $cart = Cart::instance('cart');
    $pvz = X5PostPvz::select('mdmCode', 'fullAddress as address', 'rate')->where('mdmCode', $request->code)->first();
//[
//    {
//        "vat": 0,
//        "zone": "D8",
//        "rateType": "Volgograd",
//        "rateValue": 294,
//        "rateCurrency": "RUB",
//        "rateTypeCode": "33",
//        "rateExtraValue": 48,
//        "rateValueWithVat": 294,
//        "firstMileWarehouseId": "353168ea-bef4-4a9f-9c09-890ace77b82d",
//        "rateExtraValueWithVat": 48,
//        "firstMileWarehouseName": "РЦ Волгоград"
//    }
//]

    if (!isset($pvz->rate[0]['rateValueWithVat'])||!($pvz->rate[0]['rateValueWithVat'] > 0)){
      $result = [
          'error' => 'При расчете стоимости произошла ошибка'
      ];
      return $result;
    }
    $add_price = ShippingMethod::select('add_price')->where('code', 'pochta')->first();
    $total = 0;
    $weight = 0;
    foreach($cart->content() as $item){
      $total += $item->price * $item->qty;
      $weight += ($item->options->weight ?? 100) * $item->qty;
    }
    $weight = ceil($weight / 1000) * 1000 - 3000;
    $weight = max($weight, 0);
    $price = ($pvz->rate[0]['rateValueWithVat'] + (($pvz->rate[0]['rateExtraValueWithVat'] ?? 0) * $weight)) + ($add_price->add_price ?? 0);
    $result = [
        'shippingPrice' => round($price),
        'shippingFormatPrice' => formatPrice(round($price)),
        'address' => $pvz->address,
        'total' => $total
    ];
    $cart = Cart::instance('cart');


    if(!getSettings('promo_1+1=3')){
      if($request->shipping!='cdek_courier'){
        $result['shippingPrice2'] = $result['shippingPrice'];
        $result['shippingPrice'] = 0;
      }
    }else{
      $count = $cart->content()->sum(function ($item) {
        return !$item->options->gift ? $item->qty : 0;
      });

      if($request->shipping!='cdek_courier' && $count>=3 && !Setting::whereIn('key', ['promo_1+1=3', 'happyCoupon', 'promo20', 'diamondPromo1', 'diamondPromo2'])->where('value', 1)->first()){
        $result['shippingPrice2'] = $result['shippingPrice'];
        $result['shippingPrice'] = 0;
      }
    }

    return $result;
  }

  public function calculatePochta(Request $request){
    $validate = [
        'type' => 'required|string|in:russia,international'
    ];
    if ($request->type == 'russia'){
      $validate['to'] = 'required|integer';
    }elseif($request->type == 'international'){
      $validate['country'] = 'required|integer';
    }

    $add_price = ShippingMethod::select('add_price')->where('code', 'yandex')->first();

    $request->validate($validate);
    $mass = 2000;
    $postcalculate = (new PochtaController())->calculate($request->to, $mass);
    // $postcalculate = null;
    $result = [];
    if(!$postcalculate){
      $post = new RussianPost();
      if ($request->type == 'russia'){
        $params = [
            'from' => 400066,
            'to' => $request->to,
            'mass' => $mass,
            'vat' => 0,
            'oversized' => 0,
            'month' => date('n'),
        ];
        $calculate = $post->russia($params);
        $calculate = json_decode($calculate, true);
        if (isset($calculate['pkg'])&&is_numeric($calculate['pkg'])){
          $result['price'] = $calculate['pkg'] + $add_price->add_price;
        }else{
          $result['price'] = null;
        }

      }elseif($request->type == 'international'){
        $params = [
            'country' => $request->country,
            'mass' => $mass,
            'vat' => 0,
        ];
        $calculate = $post->internation($params);
        $calculate = json_decode($calculate, true);
        if (isset($calculate['pkg_avia'])&&is_numeric($calculate['pkg_avia'])){
          $result['price'] = $calculate['pkg_avia'] + 1000;
        }elseif (isset($calculate['pkg'])&&is_numeric($calculate['pkg'])){
          $result['price'] = $calculate['pkg'] + 1000;
        }else{
          $result['price'] = null;
        }
      }
    }else{
      if ($request->type == 'russia'){
        $result['price'] = $postcalculate + $add_price->add_price;
      }elseif($request->type == 'international'){
        $result['price'] = $postcalculate + 1000;
      }
    }
    $cart = Cart::instance('cart');


    if(!getSettings('promo_1+1=3')){
      if($request->shipping!='cdek_courier'){
        $result['price2'] = $result['shippingPrice'];
        $result['price'] = 0;
      }
    }else{
      $count = $cart->content()->sum(function ($item) {
        return !$item->options->gift ? $item->qty : 0;
      });
      if($request->shipping!='cdek_courier' && $count>=3 && !Setting::whereIn('key', ['promo_1+1=3', 'happyCoupon', 'promo20', 'diamondPromo1', 'diamondPromo2'])->where('value', 1)->first()){
        $result['price2'] = $result['shippingPrice'];
        $result['price'] = 0;
      }
    }

    return $result;
  }
}
