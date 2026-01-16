<?php

namespace App\Http\Controllers\Admin\HappyCoupon;

use App\Http\Controllers\Controller;
use App\Models\GiftCoupon;
use App\Models\Prize;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrizeController extends Controller
{

    public function activePrizes(){
      $hpDate = (new GiftCoupon)->getDate();
      $prizes = Prize::query()
          ->leftJoin('gift_coupons', 'gift_coupons.prize_id', '=', 'prizes.id')
          ->leftJoin('products', 'prizes.product_id', '=', 'products.id')
          ->where('prizes.active', true)
          ->groupBy('prizes.id')
          ->orderBy('prizes.total')
          ->get(['prizes.id', 'prizes.options->thumb as thumb', 'prizes.name', 'prizes.total', 'prizes.count', 'prizes.active', 'products.name as product_name', 'products.slug as product_slug'])
          ->map(function($prize) use ($hpDate) {
            $prize->gift_coupons_count = $prize->giftCoupons()->where('created_at', '>=', $hpDate->format('Y-m-d H:i:s'))->count();
            return $prize;
          });

      $seo = [
          'title' => 'Все подарки'
      ];
      return view('template.admin.happy_coupon.prizes.activePrizes', compact('seo', 'prizes'));
    }
    public function activePrizesUpdate(Request $request){
      $request->validate([
          'count' => 'required|array',
          'count.*' => 'nullable|numeric'
      ]);
      $counts = $request->count;

      foreach($counts as $id => $count){
        $prize  = Prize::find($id);
        $prize->update([
            'count' => $count ?? 0
        ]);
        $prize->addLog('Количество изменено на '.$count);
      }
      return redirect()->route('admin.happy_coupones.activePrizes')->with([
          'status' => 'Данные успешно обновлены'
      ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prizes = Prize::where('id', '>', 127)->paginate(100);
        $seo = [
            'title' => 'Все подарки'
        ];
        return view('template.admin.happy_coupon.prizes.index', compact('seo', 'prizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $working_dir = '/shares/prizes';
      if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
        mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
      }
      $products = Product::select('id', 'sku', 'name')->where('type_id', 2)->get();
      $seo = [
          'title' => 'Добавить подарок'
      ];
      return view('template.admin.happy_coupon.prizes.create', compact('seo', 'working_dir', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'product_id' => 'nullable|exists:products,id',
        ]);
        $prize = Prize::create([
            'name' => $request->name,
            'image' => $request->options['img'] ?? null,
            'total' => $request->total ?? 0,
            'product_id' => $request->product_id,
            'active' => $request->active ?? false,
            'options' => $request->options,
            'code' => getCode(5)
        ]);
        return redirect()->route('admin.prizes.index')->with([
            'success' => 'Подарок успешно создан'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      $working_dir = '/shares/prizes';
      if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
        mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
      }
      $prize = Prize::findOrFail($id);
      $products = Product::select('id', 'sku', 'name')->where('type_id', 2)->get();
      $seo = [
          'title' => 'Редактировать '.$prize->name
      ];
      return view('template.admin.happy_coupon.prizes.edit', compact('seo', 'working_dir', 'products', 'prize'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      $request->validate([
          'name' => 'required',
          'product_id' => 'nullable|exists:products,id',
      ]);
      $prize = Prize::findOrFail($id);

      $options = $prize->options ?? [];
      $options['img'] = $request->options['img'] ?? null;
      $options['thumb'] = $request->options['thumb'] ?? null;
      $prize->update([
          'name' => $request->name,
          'image' => $request->options['img'] ?? null,
          'total' => $request->total ?? 0,
          'product_id' => $request->product_id,
          'active' => $request->active ?? false,
          'options' => $request->options,
      ]);
      return redirect()->route('admin.prizes.index')->with([
          'success' => 'Подарок успешно обновлен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


  public function set_prize($prize_id){
    $prize = Prize::find($prize_id);
    if($prize->count > 0){
      Log::debug('Количество подарков "'.$prize->name.'" больше 0');
      return false;
    }
    $hpDate = (new GiftCoupon)->getDate();
    $gave = $prize->giftCoupons()->where('created_at', '>', $hpDate)->count();
    if ($gave < $prize->total ) {
      $prize->increment('count', 1);
      Log::debug('Расписание изменило количество подарков "'.$prize->name.'" на 1');
    }else{
      Log::debug('Все подарки розданы');
    }
    return true;
  }
}
