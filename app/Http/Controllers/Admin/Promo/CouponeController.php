<?php

namespace App\Http\Controllers\Admin\Promo;

use App\Http\Controllers\Controller;
use App\Models\Coupone;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CouponeController extends Controller
{
  public function __construct()
  {
    $this->middleware('permission:Создание промокодов')->only(['create', 'store']);
    $this->middleware('permission:Редактирование промокодов')->only(['edit', 'update']);
    $this->middleware('permission:Обнуление промокодов')->only(['reset_promocode']);
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $coupones = Coupone::select();
      if (request()->coupone){
        $coupone = mb_strtolower(request()->coupone);
        $coupones->where('code', 'like', '%'.$coupone.'%');
      }
      if(request()->is_active === '1'){
        $coupones->where('used_at', null);
        $coupones->where('order_id', null);
        $coupones->where('count', '>', 0);
        $coupones->where(function ($query) {
          $query->where('available_from', '<', now());
          $query->orWhere('available_from', null);
        });
        $coupones->where(function ($query) {
          $query->where('available_until', '>', now());
          $query->orWhere('available_until', null);
        });
      }elseif(request()->is_active === '0'){
        $coupones->where(function ($query){
          $query->where('used_at', '!=', null);
          $query->orWhere('order_id', '!=', null);
          $query->orWhere('count', 0);
          $query->orWhere('available_from', '>', now());
          $query->orWhere('available_until', '<', now());
        });
      }
      if (request()->available_from_1) {
        $date_from = date('Y-m-d H:i', strtotime(request()->available_from_1));
        $coupones->where('available_from', '>=', $date_from);
      }
      if (request()->available_from_2) {
        $date_until = date('Y-m-d H:i', strtotime(request()->available_from_2));
        $coupones->where('available_from', '<=', $date_until);
      }
      if (request()->available_until_1) {
        $date_from = date('Y-m-d H:i', strtotime(request()->available_until_1));
        $coupones->where('available_until', '>=', $date_from);
      }
      if (request()->available_until_2) {
        $date_until = date('Y-m-d H:i', strtotime(request()->available_until_2));
        $coupones->where('available_until', '<=', $date_until);
      }
      if (request()->used_at_1) {
        $date_from = date('Y-m-d H:i', strtotime(request()->used_at_1));
        $coupones->where('used_at', '>=', $date_from);
      }
      if (request()->used_at_2) {
        $date_until = date('Y-m-d H:i', strtotime(request()->used_at_2));
        $coupones->where('used_at', '<=', $date_until);
      }
      if (is_numeric(request()->type)) {
        $coupones->where('type', request()->type);
      }
      $coupones = $coupones->orderBy('id', 'asc')->paginate(50);
      $seo = [
          'title' => 'Промокоды и купоны'
      ];
      return view('template.admin.coupones.index', compact('seo', 'coupones'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Создать промокод'
      ];
      $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
          ->select('products.id', 'products.sku', 'products.name', 'categories.title as category_title')
          ->where('type_id', 1)
          ->get();
      return view('template.admin.coupones.create', compact('seo', 'products'));
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
          'type' => 'required|in:1,2,4,10',
          'amount' => 'required|numeric',
          'codes' => 'required'
      ]);
      $type = $request->type;
      $errors = [];
      if ($type == 10){ // скидка в рублях на корзину

      }elseif($type == 1){ // Скдика в процентах на 1 товар
        if ($request->amount > 100){
          $errors[] = 'Скидка не может быть больше 100%';
        }
      }elseif($type == 2){ // Скдика в процентах на корзину
        if ($request->amount > 100){
          $errors[] = 'Скидка не может быть больше 100%';
        }
      }elseif($type == 4){// Скдика в процентах на выбранные товары
        if ($request->amount > 100){
          $errors[] = 'Скидка не может быть больше 100%';
        }
      }
      if (!empty($errors)){
        return back()->withInput()->withErrors($errors);
      }
      $codes = explode("\n", $request->codes);
      $insert_data = [];
      $already_exist = [];
      foreach($codes as $code){
        if (count($insert_data)>999){
          break;
        }
        $code = preg_replace('/\s+/', '', $code);
        $code = mb_strtolower($code);

        if($request->available_until){
          $available_until = Carbon::createFromFormat('d.m.Y', $request->available_until)->format('Y-m-d').' 23:59:59';
        }else{
          $available_until = now()->addMonths(6)->format('d.m.Y').' 23:59:59';
        }

        $promocode_params = [
            'code' => $code,
            'type' => $type,
            'amount' => $request->amount,
            'count' => 1,
            'available_until' => $available_until
        ];
        try{
          $coupone = Coupone::create($promocode_params);
        }catch (QueryException $exception){
          $message = $exception->getMessage();
          if (strpos($message, 'Integrity constraint violation') !== false){
            $already_exist[] = 'Промокод '.$code.' уже существует';
          }else{
            $already_exist[] = $message;
          }
          continue;
        }
        if($type == 4){// Скдика в процентах на выбранные товары
          $coupone->products()->sync($request->products);
        }else{
          $coupone->products()->sync([]);
        }
        $coupone->addLog('Создан промокод', null, $promocode_params);
      }

      return redirect()->route('admin.coupones.index')->with([
          'status' => 'Купоны на скидку успешно добавлены'
      ])->withErrors($already_exist);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupone $coupone)
    {
      $seo = [
          'title' => 'Обновить промокод'
      ];
      $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
          ->select('products.id', 'products.sku', 'products.name', 'categories.title as category_title')
          ->where('type_id', 1)
          ->get();
      return view('template.admin.coupones.edit', compact('seo', 'coupone', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupone $coupone)
    {
      $request->validate([
          'type' => 'required|in:1,2,4,10',
          'amount' => 'required|numeric',
          'code' => 'required|string'
      ]);
      $type = $request->type;
      $errors = [];
      if ($type == 10){ // скидка в рублях на корзину

      }elseif($type == 1){ // Скдика в процентах на 1 товар
        if ($request->amount > 100){
          $errors[] = 'Скидка не может быть больше 100%';
        }
      }elseif($type == 2){ // Скдика в процентах на корзину
        if ($request->amount > 100){
          $errors[] = 'Скидка не может быть больше 100%';
        }
      }elseif($type == 4){// Скдика в процентах на выбранные товары
        if ($request->amount > 100){
          $errors[] = 'Скидка не может быть больше 100%';
        }
      }
      if(!empty($errors)){
        return back()->withErrors($errors);
      }
      if($request->available_until){
        $available_until = Carbon::createFromFormat('d.m.Y', $request->available_until)->format('Y-m-d').' 23:59:59';
      }else{
        $available_until = now()->format('Y-m-d').' 23:59:59';
      }
      $old = $coupone->toArray();
      $coupone->update([
          'code' => $request->code,
          'type' => $request->type,
          'amount' => $request->amount,
          'count' => $request->count,
          'available_until' => $available_until,
      ]);
      if($type == 4){// Скдика в процентах на выбранные товары
        $coupone->products()->sync($request->products);
      }else{
        $coupone->products()->sync([]);
      }
      $coupone->addLog('Данные промокода изменены', $request->comment, [
          'old' => $old,
          'new' => $coupone->toArray(),
      ]);
      return redirect()->route('admin.coupones.index')->with([
          'status' => 'Купон "'.$request->code.'" успешно обновлен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupone $coupone)
    {
        //
    }

  public function reset_promocode(Coupone $coupone, Request $request){
    $old = $coupone->toArray();
    $coupone->update([
        'amount' => 0,
        'count' => 0,
    ]);
    $coupone->addLog('Обнулен промокод', $request->comment, $old);
    return redirect()->route('admin.coupones.index')->with([
        'status' => 'Промокод "'.$coupone->code.'" успешно обнулен'
    ]);
  }
}
