<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class ProductSkuController extends Controller
{
  public function index()
  {
    $items = ProductSku::query()->withCount('products')->orderBy('name')->paginate(30);
    $seo = [
        'title' => 'Артикулы'
    ];
    return view('template.admin.product-skus.index', compact('seo', 'items'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
        'name' => 'required|unique:product-skus,name'
    ]);

    ProductSku::create([
        'name' => mb_strtolower($request->post('name'))
    ]);
    return back()->with([
        'success' => 'Новое направление «'.$request->post('name').'» успешно добавлен'
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ProductSku $productSku)
  {
    $request->validate([
        'name' => 'required'
    ]);
    if(ProductSku::query()->where('name', mb_strtolower($request->post('name')))->where('id', '!=', $productSku->id)->exists()){
      return back()->withErrors(['Артикул с данным именем уеж существует']);
    }
    $productSku->update([
        'name' => mb_strtolower($request->post('name'))
    ]);
    return back()->with([
        'success' => 'Артикул «'.$request->post('name').'» успешно изменен'
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ProductSku $productSku)
  {
    $productSku->delete();
    return back()->with([
        'success' => 'Артикул успешно удален'
    ]);
  }
}
