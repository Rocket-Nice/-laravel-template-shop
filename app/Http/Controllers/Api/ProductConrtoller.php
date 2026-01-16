<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductConrtoller extends Controller
{
    public function all(Request $request){
      $request->validate([
          'name' => 'nullable|string|max:250',
          'category_id' => 'nullable|numeric|exists:categories,id'
      ]);
      $query = Product::query();

      // Фильтрация по имени продукта
      if ($request->has('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
      }

      // Фильтрация по названию категории
      if ($request->has('category')) {
        $query->where('category_id', $request->category_id);
      }

      $products = $query->whereIn('type_id', [1,5,6])->get(['id', 'name', 'sku', 'price', 'category_id', 'style_page->cardImage->image->200 as image', 'slug'])
          ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'category_name' => $product->category ? $product->category->title : null,
                'image' => $product->image,
                // 'slug' => $product->slug,
              ];
            });

      return response()->json($products);
    }

    public function getName(Request $request){
      $request->validate([
          'product_id' => 'required|exists:products,id'
      ]);
      $products = Product::query()->where('id', $request->product_id)->get(['id', 'name', 'sku', 'price', 'category_id', 'style_page->cardImage->image->200 as image', 'slug'])
          ->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'category_name' => $product->category ? $product->category->title : null,
                'image' => $product->image,
                // 'slug' => $product->slug,
            ];
          });
      return response()->json($products->first());
    }
}
