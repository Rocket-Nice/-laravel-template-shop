<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Content;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use SafeObject;

class ProductGroupController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $productGroups = ProductGroup::query()
        ->with('pages')
        ->withCount('products')
        ->paginate(30);
    $seo = [
        'title' => 'Группы товаров'
    ];
    return view('template.admin.product_groups.index', compact('productGroups', 'seo'));
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $contents = Content::query()->select('id', 'title', 'route')->get();
    $categories = Category::select('id', 'title', 'category_id')->get();
    $seo = [
        'title' => 'Добавить группу товаров'
    ];
    return view('template.admin.product_groups.create', compact('contents', 'categories', 'seo'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
        'name' => 'required'
    ]);

    $productGroup = ProductGroup::create([
        'name' => $request->name,
        'description' => $request->description,
        'category_id' => $request->category_id,
    ]);
    if($request->get('content')){
      $productGroup->pages()->sync($request->get('content'));
    }

    if($request->get('products')){
      $productGroup->products()->sync($request->get('products'));
      if($request->get('category_id')) {
        $category = Category::find($request->get('category_id'));
        $category->many_products()->sync(array_keys($request->get('products')));
        $products = Product::query()
            ->select('id', 'subcategories')
            ->whereIn('id', array_keys($request->get('products')))
            ->get();
        foreach($products as $product){
          $product->update([
              'subcategories' => $product->categories()->pluck('id')->toArray()
          ]);
        }
      }
       Product::flushQueryCache();
    }

    return redirect()->route('admin.product-group.index')->with([
        'success' => 'Группа продуктов успешно добавлена'
    ]);
  }


  /**
   * Show the form for editing the specified resource.
   */
  public function edit(ProductGroup $productGroup)
  {
    $contents = Content::query()->select('id', 'title', 'route')->get();
    $categories = Category::select('id', 'title', 'category_id')->get();
    $products = $productGroup->products()->with(['category' => function($query){
      $query->select('categories.id', 'categories.title');
    }])->select('products.id', 'products.sku', 'products.name', 'products.category_id');
    $products = $products->orderByPivot('order', 'asc')->orderBy('name')->get();
    $seo = [
        'title' => 'Добавить группу товаров'
    ];
    return view('template.admin.product_groups.edit', compact('productGroup', 'contents', 'categories', 'seo', 'products'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, ProductGroup $productGroup)
  {
    $request->validate([
        'name' => 'required'
    ]);
    $productGroup->update([
        'name' => $request->name,
        'description' => $request->description,
        'category_id' => $request->category_id,
    ]);
    if($request->get('content')){
      $productGroup->pages()->sync($request->get('content'));
    }
    if($request->get('products')){
      $productGroup->products()->sync($request->get('products'));
      if($request->get('category_id')) {
        $category = Category::find($request->get('category_id'));
        $category->many_products()->sync(array_keys($request->get('products')));
        $products = Product::query()
            ->select('id', 'subcategories')
            ->whereIn('id', array_keys($request->get('products')))
            ->get();
        foreach($products as $product){
          $product->update([
              'subcategories' => $product->categories()->pluck('id')->toArray()
          ]);
        }
      }
       Product::flushQueryCache();
    }

    return redirect()->route('admin.product-group.index')->with([
        'success' => 'Группа продуктов успешно обновлена'
    ]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(ProductGroup $productGroup)
  {
    $productGroup->delete();
    Product::flushQueryCache();
    return redirect()->route('admin.product-group.index')->with([
        'success' => 'Группа продуктов удалена'
    ]);
  }

  public function getProducts(Request $request)
  {
    $products = Product::query()->filtered(new SafeObject(request()->toArray()))->with(['category' => function($query){
      $query->select('id', 'title');
    }])->select('id', 'sku', 'name', 'category_id');
    if($request->category){
      $products->where('category_id', $request->category);
    }
    $products = $products->orderBy('category_id', 'asc')->orderBy('name', 'asc')->get();
    return response()->json($products);
  }
}
