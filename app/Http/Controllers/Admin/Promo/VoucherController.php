<?php

namespace App\Http\Controllers\Admin\Promo;

use App\Http\Controllers\Controller;
use App\Jobs\CreateVouchersJob;
use App\Models\Coupone;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class VoucherController extends Controller
{
  public function __construct()
  {
    $this->middleware('permission:Создание подарочных сертификатов')->only(['create', 'store']);
    $this->middleware('permission:Редактирование подарочных сертификатов')->only(['edit', 'update']);
    $this->middleware('permission:Обнуление подарочных сертификатов')->only(['reset_voucher']);
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $vouchers = Voucher::select();
      if (request()->voucher){
        $voucher = mb_strtolower(request()->voucher);
        $vouchers->where('code', 'like', '%'.$voucher.'%');
      }
      $vouchers = $vouchers->orderBy('id', 'asc')->paginate(50);
      $seo = [
          'title' => 'Подарочные сертификаты'
      ];
      return view('template.admin.vouchers.index', compact('seo', 'vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Создать подарочный сертификат'
      ];
      return view('template.admin.vouchers.create', compact('seo'));
    }

    public function batch_create()
    {
      $seo = [
          'title' => 'Создать подарочные сертификаты'
      ];
      return view('template.admin.vouchers.batch_create', compact('seo'));
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
          'code' => 'required|unique:vouchers,code',
          'amount' => 'required|numeric',
          'available_from' => 'date',
          'available_until' => 'date'
      ]);
      if($request->available_from){
        $available_from = Carbon::createFromFormat('d.m.Y', $request->available_from)->format('Y-m-d').' 00:00:00';
      }else{
        $available_from = now()->format('Y-m-d').' 00:00:00';
      }
      if($request->available_until){
        $available_until = Carbon::createFromFormat('d.m.Y', $request->available_until)->format('Y-m-d').' 23:59:59';
      }else{
        $available_until = now()->format('Y-m-d').' 23:59:59';
      }
      $voucher_data = [
          'code' => $request->code,
          'amount' => $request->amount,
          'type' => 1,
          'count' => 1,
          'available_until' => $available_until,
          'available_from' => $available_from,
          'options' => [
              'created_by' => auth()->id()
          ],
          'save_amount' => $request->save_amount ?? false,
      ];
      $voucher = Voucher::create($voucher_data);
      $voucher->addLog('Создан подарочный сертификат', null, $voucher_data);
      return redirect()->route('admin.vouchers.index')->with([
          'status' => 'Сертификат '.$request->code.' успешно создан'
      ]);
    }


  public function batch_store(Request $request)
  {
    $request->validate([
        'amount' => 'required|unique:vouchers,code',
        'count' => 'required|numeric',
    ]);
    if(auth()->id()==1){
      CreateVouchersJob::dispatch($request->amount, $request->count)->onQueue('create_vouchers');
      dd('сертификаты будут созданы и доступны для скачивания');
    }

    $count = (int)$request->count < 50 ? (int)$request->count : 50;
    $vouchers_data = $this->create_voucher($request->amount, $request->count);

    $seo = [
        'title' => 'Сертификаты успешно созданы'
    ];
    return view('template.admin.vouchers.batch_result', compact('seo', 'vouchers_data'));
  }

  private function getVoucherName(){
    $voucher = getCode(4, true).'-'.getCode(4, true).'-'.getCode(4, true);
    $testVoucher = Voucher::where('code', '=', $voucher)->count();
    if ($testVoucher > 0) {
      self::getVoucherName();
    }else{
      return $voucher;
    }
  }

  public function create_voucher($id, $count){
    $i = 0;
    $vouchers = [
        500 => 'voucher500.png',
        1000 => 'voucher1000.png',
        3000 => 'voucher3000.png',
        5000 => 'voucher5000.png',
        7000 => 'voucher7000.png',
        10000 => 'voucher10000.png',
        50000 => 'voucher50000.png',
        '1000n' => 'new1000.png',
        '1000nn' => 'sert1000sep.png',
    ];
    if(!isset($vouchers[$id])){
      return false;
    }

    $path = 'app/public/vouchers/'.now()->format('Y-m-d').'_'.$id.'/';
    if (!file_exists(storage_path($path))) {
      mkdir(storage_path($path), 0777, true);
    }
    $amount = $id;
    if($id=='1000n'){
      $amount = 1000;
    }elseif($id == '1000nn'){
      $amount = 1000;
    }
    $result = [];
    while ($i < $count) {
      $voucher = $this->getVoucherName();

      $img = Image::make(public_path('img/vch/' . $vouchers[$id]));
//
      if ($id == '1000n'){
        $img->text(mb_strtoupper($voucher), 1120, 840, function ($font) {
          $font->file(public_path('img/vch/CormorantInfant-Regular.ttf'));
          $font->size(50);
          $font->color('#000');
        });
      }elseif($id == '1000nn'){
        $img->text('КОД СЕРТИФИКАТА: ' . mb_strtoupper($voucher), 280, 1605, function ($font) {
          $font->file(public_path('img/vch/CormorantInfant-Regular.ttf'));
          $font->size(40);
          $font->color('#000');
        });
      }else{
        $img->text('НОМЕР СЕРТИФИКАТА: ' . mb_strtoupper($voucher), 959, 477, function ($font) {
          $font->file(public_path('img/vch/CormorantInfant-Regular.ttf'));
          $font->size(35);
          $font->color('#000');
        });
      }

      $img_link = storage_path($path . $voucher . '.png');
      $img->save($img_link);
      $new_promo = Voucher::create([
          'code' => $voucher,
          'type' => 1,
          'count' => 1,
          'amount' => $amount,
          'options' => [
              'file' => $img_link,
              'link' => storageToAsset($img_link),
              'created_by' => auth()->id()
          ]
      ]);
      $result[] = [$new_promo->amount, $new_promo->code, storageToAsset($img_link)];
      $i++;
    }
    Log::debug(print_r($result, true));
    return $result;
  }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
      $seo = [
          'title' => 'Обновить подарочный сертификат'
      ];
      return view('template.admin.vouchers.edit', compact('seo', 'voucher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Voucher $voucher)
    {
      $request->validate([
          'code' => 'required',
          'amount' => 'required|numeric',
          'available_from' => 'date',
          'available_until' => 'date'
      ]);
      if($request->available_from){
        $available_from = Carbon::createFromFormat('d.m.Y', $request->available_from)->format('Y-m-d').' 00:00:00';
      }else{
        $available_from = now()->format('Y-m-d').' 00:00:00';
      }
      if($request->available_until){
        $available_until = Carbon::createFromFormat('d.m.Y', $request->available_until)->format('Y-m-d').' 23:59:59';
      }else{
        $available_until = now()->format('Y-m-d').' 23:59:59';
      }
      $voucher_data = [
          'code' => $request->code,
          'amount' => $request->amount,
          'available_until' => $available_until,
          'available_from' => $available_from,
          'save_amount' => $request->save_amount ?? false,
      ];
      $old = $voucher->toArray();
      $voucher->update($voucher_data);
      $voucher->addLog('Подарочный сертификат изменен', $request->comment, [
          'old' => $old,
          'new' => $voucher->toArray(),
      ]);
      return redirect()->route('admin.vouchers.index')->with([
          'status' => 'Сертификат '.$request->code.' успешно обновлен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        //
    }

  public function reset_voucher(Voucher $voucher, Request $request){
    $old = $voucher->toArray();
    $voucher->update([
        'amount' => 0,
        'count' => 0,
    ]);
    $voucher->addLog('Обнулен подарочный сертификат', $request->comment, $old);
    return redirect()->route('admin.vouchers.index')->with([
        'status' => 'Промокод "'.$voucher->code.'" успешно обнулен'
    ]);
  }
}
