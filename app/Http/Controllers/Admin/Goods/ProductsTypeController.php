<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\ProductCourse;
use App\Models\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductsTypeController extends Controller
{
    public function index(Request $request){
      $product_types = ProductType::query()->withCount('products');
      if($request->keyword){
        $keyword = trim($request->keyword);
        $product_types->where(function ($query) use ($keyword) {
          $keyword = mb_strtolower($keyword);
          $query->where(DB::raw('lower(name)'), 'like', '%'.$keyword.'%')
              ->orWhere(DB::raw('lower(article)'), 'like', '%'.$keyword.'%');
          return $query;
        });
      }
      $product_types = $product_types->orderBy('created_at', 'desc')->paginate(50);
      $seo = [
          'title' => 'Типы продуктов'
      ];
      return view('template.admin.product_types.index', compact('product_types', 'seo'));
    }

    public function create(){
      $product_types = ProductType::all();
      $seo = [
          'title' => 'Добавить тип продукта'
      ];
      return view('template.admin.product_types.create', compact('seo', 'product_types'));
    }

  public function store(Request $request)
  {
    $request->validate([
        'name' => 'required|string'
    ]);


    $params_store = [
        'name' => $request->name,
    ];

    ProductType::create($params_store);
    ProductType::flushQueryCache();
    return redirect()->route('admin.product_types.index')->with([
        'success' => 'Тип продуктов успешно добавлен'
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(ProductType $product_type)
  {
    $seo = [
        'title' => 'Изменить тип продукта'
    ];
    return view('template.admin.product_types.edit', compact('seo', 'product_type'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, ProductType $product_type)
  {
    $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:product_types,id'
    ]);

    $params_update['title'] = $request->title;
    $params_update['description'] = $request->description;
    $params_update['category_id'] = $request->category_id ?? null;

    $product_type->update($params_update);

    ProductType::flushQueryCache();
    return redirect()->route('admin.product_types.index')->with([
        'success' => 'Тип продуктов успешно обновлен'
    ]);
  }
}
