<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCourse;
use App\Services\CompressModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index(Request $request){
      $categories = Category::query()->withCount('products');
      if($request->keyword){
        $keyword = trim($request->keyword);
        $categories->where(function ($query) use ($keyword) {
          $keyword = mb_strtolower($keyword);
          $query->where(DB::raw('lower(title)'), 'like', '%'.$keyword.'%');
          return $query;
        });
      }
      $categories = $categories->orderBy('created_at', 'desc')->paginate(50);
      $seo = [
          'title' => 'Категории товаров'
      ];
      return view('template.admin.categories.index', compact('categories', 'seo'));
    }

    public function create(){
      $categories = Category::select('id', 'title', 'category_id')->get();
      $seo = [
          'title' => 'Добавить категорию'
      ];
      return view('template.admin.categories.create', compact('seo', 'categories'));
    }

  public function store(Request $request)
  {
    $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id'
    ]);

    $slug = translit($request->title);
    if (Category::where('slug', $slug)->count()){
      $slug .= '_'.mb_strtolower(getCode(3));
    }
    $params_store = [
        'title' => $request->title,
        'description' => $request->description,
        'slug' => $slug,
        'category_id' => $request->category_id,
        'template' => 'none',
        'hidden' => $request->hidden ?? false,
        'order' => $request->order,
    ];

    Category::create($params_store);
    Category::flushQueryCache();
    return redirect()->route('admin.categories.index')->with([
        'success' => 'Категория успешно добавлена'
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit(Category $category)
  {
    $seo = [
        'title' => 'Изменить категорию'
    ];
    $categories = Category::select('id', 'title', 'category_id')->get();

    $working_dir = '/shares/content/'.$category->slug;
    if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
      mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
    }

    return view('template.admin.categories.edit', compact('seo', 'category', 'categories', 'working_dir'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Category $category)
  {
    $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id'
    ]);

    $params_update['title'] = $request->title;
    $params_update['description'] = $request->description;
    $params_update['category_id'] = $request->category_id ?? null;
    $params_update['hidden'] = $request->hidden ?? false;
    $params_update['order'] = $request->order;
    $params_update['options'] = $request->options;

    if(isset($request->options['categoryImage']['img'])){
      $compressedImages = CompressModule::compressImage($request->options['categoryImage']['img'], [200,480,768,1200,1920], 1920);
      $params_update['options']['categoryImage']['size'] = $compressedImages;
    }
    if(isset($request->options['menuImage']['img'])){
      $compressedImages = CompressModule::compressImage($request->options['menuImage']['img'], [200,480,768], 768);
      $params_update['options']['menuImage']['size'] = $compressedImages;
    }
    $category->update($params_update);

    Category::flushQueryCache();
    return redirect()->route('admin.categories.index')->with([
        'success' => 'Категория успешно обновлена'
    ]);
  }
}
