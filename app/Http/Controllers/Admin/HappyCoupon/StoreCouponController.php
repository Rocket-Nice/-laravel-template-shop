<?php

namespace App\Http\Controllers\Admin\HappyCoupon;

use App\Http\Controllers\Controller;
use App\Models\GiftCode;
use App\Models\Pickup;
use App\Models\Prize;
use App\Models\StoreCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;

class StoreCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $storeCoupons = StoreCoupon::query()
          ->leftJoin('users', 'store_coupons.user_id', '=', 'users.id')
          ->leftJoin('orders', 'store_coupons.order_id', '=', 'orders.id')
          ->leftJoin('pickups', 'store_coupons.pickup_id', '=', 'pickups.id')
          ->select(
              'store_coupons.*',
              'users.email as user_email',
              'orders.slug as order_slug',
              'pickups.params->short_name as pickup_short_name',
          );
      if(request()->date_from||request()->date_until){
        $storeCoupons->whereNotNull('store_coupons.order_id');
        $storeCoupons->whereNotNull('store_coupons.user_id');
      }
      if (request()->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
        $storeCoupons->where('orders.created_at', '>', $date_from);
      }
      if (request()->date_until) {
        $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
        $storeCoupons->where('orders.created_at', '<', $date_until);
      }

      if($request->get('code')){
        $code = mb_strtolower($request->get('code'));
        $storeCoupons->where(DB::raw('lower(store_coupons.code)'), 'like', '%'.$code.'%');
      }
      if($request->get('order')){
        $storeCoupons->where('order_id', $request->get('order'));
      }
      if($request->get('email')){
        $storeCoupons->where('users.email', $request->get('email'));
      }
      if($request->get('pickups')){
        $storeCoupons->whereIn('store_coupons.pickup_id', $request->get('pickups'));
      }
      $storeCoupons = $storeCoupons->orderBy('order_id', 'desc')->orderBy('id', 'asc')->paginate(100);
      $pickups = Pickup::whereIn('code', ['pickup_msk', 'pickup_vlg', 'pickup_per', 'pickup_msk2', 'pickup_vlzh', 'pickup_smr', 'pickup_krd', 'pickup_kzn', 'pickup_spb', 'pickup_ufa', 'pickup_nn', 'pickup_nsk', 'pickup_tmn'])->get();
      $seo = [
          'title' => 'Подарочные коды магазинов'
      ];
      return view('template.admin.happy_coupon.storeCoupons.index', compact('seo', 'storeCoupons', 'pickups'));
    }

    public function makeStoreCoupons()
    {
      deleteDirectory(storage_path('app/public/store_coupons'));
      $xy = [
          [22, 50],
          [88, 50],
          [155, 50],
          [22, 141],
          [88, 141],
          [155, 141],
          [22, 233],
          [88, 233],
          [155, 233],
      ];
      $data = [
        'afimall' => [
            'pickup' => 'pickup_msk',
            'name' => 'AFL',
            3500 => 100,
            7000 => 60,
        ],
        'marmelad' => [
            'pickup' => 'pickup_vlg',
            'name' => 'MRM',
            3500 => 100,
            7000 => 60,
        ],
        'perm' => [
            'pickup' => 'pickup_per',
            'name' => 'PER',
            3500 => 100,
            7000 => 60,
        ],
        'kapitoliy' => [
            'pickup' => 'pickup_msk2',
            'name' => 'KAP',
            3500 => 60,
            7000 => 40,
        ],
        'volzhsky' => [
            'pickup' => 'pickup_vlzh',
            'name' => 'VLZ',
            3500 => 40,
            7000 => 30,
        ],
        'samara' => [
            'pickup' => 'pickup_smr',
            'name' => 'SMR',
            3500 => 60,
            7000 => 40,
        ],
        'karasnodar' => [
            'pickup' => 'pickup_krd',
            'name' => 'KRD',
            3500 => 60,
            7000 => 40,
        ],
        'kazan' => [
            'pickup' => 'pickup_kzn',
            'name' => 'KZN',
            3500 => 60,
            7000 => 40,
        ],
        'stpeterburg' => [
            'pickup' => 'pickup_spb',
            'name' => 'SPB',
            3500 => 60,
            7000 => 40,
        ],
        'ufa' => [
            'pickup' => 'pickup_ufa',
            'name' => 'UFA',
            3500 => 60,
            7000 => 40,
        ],
        'nnovgorod' => [
            'pickup' => 'pickup_nn',
            'name' => 'NGR',
            3500 => 60,
            7000 => 40,
        ],
        'novosibirsk' => [
            'pickup' => 'pickup_nsk',
            'name' => 'NSK',
            3500 => 60,
            7000 => 40,
        ],
        'tumen' => [
            'pickup' => 'pickup_tmn',
            'name' => 'TMN',
            3500 => 60,
            7000 => 40,
        ],
      ];
//      $data = [
//          'afimall' => [
//              'pickup' => 'pickup_msk',
//              'name' => 'AFL',
//              3500 => 9,
//              7000 => 9,
//          ]
//      ];
      foreach($data as $key => $store_info){
        $template = public_path('happy_coupon/A4_2.pdf');
        $storage = storage_path('app/public/store_coupons');

        $path = $storage.'/'.$key;
        if (!file_exists($path)) {
          mkdir($path, 0777, true);
        }
        // 3500
        $pdfFiles3500 = glob($path.'/'.$key.'3500-*.pdf');
        $pdfCount3500 = count($pdfFiles3500);
        $count3500 = $store_info[3500];
        $pages = (int)ceil($count3500/9) - $pdfCount3500;
        $i = 0;
        while ($i < $pages){
          $item = 0;
          // Создайте экземпляр FPDI
          $pdf = new FPDI();
          // Установите количество страниц в вашем PDF-файле
          $pageCount = $pdf->setSourceFile($template);
          $templateId = $pdf->importPage(1);
          $size = $pdf->getTemplateSize($templateId);
          // Добавьте новую страницу с теми же размерами
          $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
          $pdf->useTemplate($templateId);
          $cat = $store_info['name'].'3500';

          // $pdf->AddFont('CormorantInfant','',public_path('img/vch/CormorantInfant-Regular.ttf'),true);
          $pdf->SetFont('Arial', '', 9);
          $pdf->SetTextColor(0, 0, 0);
          while ($item < 9){
            $gift_code = strtoupper($this->getCode($cat));
            $pickup = Pickup::where('code', $store_info['pickup'])->first();
            StoreCoupon::create([
                'code' => $gift_code,
                'pickup_id' => $pickup->id,
            ]);
            $pdf->SetXY($xy[$item][0], $xy[$item][1]);
            $pdf->Write(0, $gift_code);
            $item++;
          }
          $i++;
          $pdfName = $key.'3500-'.($pdfCount3500+$i).'.pdf';
          $pdf->Output('F', $path.'/'.$pdfName);
        }


        // 7000
        $pdfFiles7000 = glob($path.'/'.$key.'7000-*.pdf');
        $pdfCount7000 = count($pdfFiles7000);

        $count7000 = $store_info[7000];
        $pages = (int)ceil($count7000/9) - $pdfCount7000;
        $i = 0;
        while ($i < $pages){
          $item = 0;
          // Создайте экземпляр FPDI
          $pdf = new FPDI();
          // Установите количество страниц в вашем PDF-файле
          $pageCount = $pdf->setSourceFile($template);
          $templateId = $pdf->importPage(1);
          $size = $pdf->getTemplateSize($templateId);
          // Добавьте новую страницу с теми же размерами
          $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
          $pdf->useTemplate($templateId);
          $cat = $store_info['name'].'7000';

          // $pdf->AddFont('CormorantInfant','',public_path('img/vch/CormorantInfant-Regular.ttf'),true);
          $pdf->SetFont('Arial', '', 9);
          $pdf->SetTextColor(255, 255, 255);
          while ($item < 9){
            $gift_code = strtoupper($this->getCode($cat));
            StoreCoupon::create([
                'code' => $gift_code,
                'pickup_id' => $pickup->id,
            ]);
            $pdf->SetXY($xy[$item][0], $xy[$item][1]);
            $pdf->Write(0, $gift_code);
            $item++;
          }
          $i++;
          $pdfName = $key.'7000-'.($pdfCount7000+$i).'.pdf';
          $pdf->Output('F', $path.'/'.$pdfName);
        }
      }
      return 12;
    }

  private function getCode($cat){
    $code = $cat.'-'.getCode(4, true).'-'.getCode(4, true);
    $testCode = StoreCoupon::where('code', '=', $code)->count();
    if ($testCode > 0) {
      self::getCode($cat);
    }else{
      return $code;
    }
  }
}
