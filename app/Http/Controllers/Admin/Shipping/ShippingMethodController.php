<?php

namespace App\Http\Controllers\Admin\Shipping;

use App\Http\Controllers\Controller;
use App\Imports\CdekNewTerritoryImport;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipping_methods = ShippingMethod::paginate(50);
        $seo = [
            'title' => 'Способы доставки'
        ];
        return view('template.admin.shipping.shipping_methods.index', compact('seo', 'shipping_methods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Добавить способ доставки'
      ];
      return view('template.admin.shipping.shipping_methods.create', compact('seo'));
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
          'code' => 'string|required',
          'name' => 'string|required',
          'add_price' => 'numeric|required',
          'active' => 'boolean|nullable'
      ]);
      ShippingMethod::create([
          'code' => $request->code,
          'name' => $request->name,
          'add_price' => $request->add_price ?? null,
          'active' => $request->active ?? false
      ]);
      ShippingMethod::flushQueryCache();
      return redirect()->route('admin.shipping_methods.index')->with([
          'success' => 'Новый способ доставки создан'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShippingMethod  $shippingMethod
     * @return \Illuminate\Http\Response
     */
    public function edit(ShippingMethod $shippingMethod)
    {
      $seo = [
          'title' => 'Изменить способ доставки'
      ];
      return view('template.admin.shipping.shipping_methods.edit', compact('seo', 'shippingMethod'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShippingMethod  $shippingMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShippingMethod $shippingMethod)
    {
      $request->validate([
          'code' => 'string|required',
          'name' => 'string|required',
          'add_price' => 'numeric|required',
          'active' => 'boolean|nullable'
      ]);
      $shippingMethod->update([
          'code' => $request->code,
          'name' => $request->name,
          'add_price' => $request->add_price ?? null,
          'active' => $request->active ?? false
      ]);
      ShippingMethod::flushQueryCache();
      return redirect()->route('admin.shipping_methods.index')->with([
          'success' => 'Способ доставки изменен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShippingMethod  $shippingMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingMethod $shippingMethod)
    {
        //
    }


  public function importNewTerritories(Request $request){
    $request->validate([
        'file' => 'required|mimes:xlsx',
    ]);

    try {
      Excel::import(new CdekNewTerritoryImport(), $request->file('file'));
    } catch (Throwable $e) {
      return back()->withErrors([
          'file' => 'Ошибка при импорте файла '.$e->getMessage()
      ]);
    }
    return redirect()->route('admin.shipping_methods.index')->with([
        'success' => 'Даные успешно импортированы'
    ]);
  }
}
