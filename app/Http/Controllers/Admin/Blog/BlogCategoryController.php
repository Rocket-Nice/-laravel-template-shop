<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Services\CompressModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogCategoryController extends Controller
{
    public function index()
    {
      $categories = BlogCategory::query()->withCount('articles');
      if(request()->keyword){
        $keyword = trim(request()->keyword);
        $categories->where(function ($query) use ($keyword) {
          $keyword = mb_strtolower($keyword);
          $query->where(DB::raw('lower(name)'), 'like', '%'.$keyword.'%');
          return $query;
        });
      }
      $categories = $categories->orderBy('created_at', 'desc')->paginate(50);
      $seo = [
          'title' => 'Разделы новостей'
      ];
      return view('template.admin.blog.categories.index', compact('categories', 'seo'));
    }

  public function create(){
    $seo = [
        'title' => 'Добавить раздел'
    ];
    return view('template.admin.blog.categories.create', compact('seo'));
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
      $request->validate([
          'name' => 'required|string',
      ]);

      $slug = translit($request->name);
      if (BlogCategory::query()->where('slug', $slug)->count()){
        $slug .= '_'.mb_strtolower(getCode(3));
      }

      $params_store = [
          'name' => $request->name,
          'status' => $request->status,
          'slug' => $slug,
      ];

      BlogCategory::create($params_store);
      BlogCategory::flushQueryCache();
      return redirect()->route('admin.blog.categories.index')->with([
          'success' => 'Новый раздел успешно добавлен'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogCategory  $category
     */
    public function edit(BlogCategory $category)
    {
      $seo = [
          'title' => 'Изменить раздел'
      ];
      $working_dir = '/shares/blog/'.$category->slug;
      if (!file_exists(storage_path('app/public/blog'.$working_dir))) {
        mkdir(storage_path('app/public/blog'.$working_dir), 0777, true);
      }
      return view('template.admin.blog.categories.edit', compact('seo', 'category', 'working_dir'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogCategory  $category
     */
    public function update(Request $request, BlogCategory $category)
    {
      $request->validate([
          'name' => 'required|string',
      ]);
      $params_update = [];
      $params_update['name'] = $request->name;
      $params_update['status'] = $request->status;
      $params_update['data'] = $request->data;
      if(isset($request->data['image']['img'])){
        $compressedImages = CompressModule::compressImage($request->data['image']['img'], [200,480,768,1200,1920], 480);
        $params_update['data']['image']['size'] = $compressedImages;
      }

      $category->update($params_update);

      BlogCategory::flushQueryCache();
      return redirect()->route('admin.blog.categories.index')->with([
          'success' => 'Раздел успешно обновлен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(BlogCategory $category)
    {
        //
    }
}
