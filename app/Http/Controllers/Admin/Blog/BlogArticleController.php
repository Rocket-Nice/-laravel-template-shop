<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Jobs\CompressArticleImagesJob;
use App\Jobs\CompressContentImagesJob;
use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\Content;
use App\Models\Product;
use App\Services\CompressModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlogArticleController extends Controller
{
    public function index()
    {
      $articles = BlogArticle::query();
      if(request()->keyword){
        $keyword = trim(request()->keyword);
        $articles->where(function ($query) use ($keyword) {
          $keyword = mb_strtolower($keyword);
          $query->where(DB::raw('lower(title)'), 'like', '%'.$keyword.'%');
          return $query;
        });
      }
      $articles = $articles->orderBy('created_at', 'desc')->paginate(50);
      $seo = [
          'title' => 'Публикации'
      ];
      return view('template.admin.blog.articles.index', compact('articles', 'seo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Добавить публикацию'
      ];
      return view('template.admin.blog.articles.create', compact('seo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
      $request->validate([
          'title' => 'required|string',
      ]);

      $slug = translit($request->title);
      if (BlogArticle::query()->where('slug', $slug)->count()){
        $slug .= '_'.mb_strtolower(getCode(3));
      }

      $params_store = [
          'title' => $request->title,
          'status' => 0,
          'slug' => $slug,
      ];

      BlogArticle::create($params_store);
      BlogArticle::flushQueryCache();
      return redirect()->route('admin.blog.articles.edit', $slug);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogArticle  $article
     */
    public function edit(BlogArticle $article)
    {
      $seo = [
          'title' => 'Редактор публикации'
      ];
      $working_dir = '/shares/blog/'.$article->slug;
      if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
        mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
      }
      $categories = BlogCategory::query()->select('id', 'name')->get();
      $products = Product::join('categories', 'products.category_id', '=', 'categories.id')
          ->select('products.id', 'products.sku', 'products.name', 'categories.title as category_title')
          ->where('type_id', 1)
          ->get();
      return view('template.admin.blog.articles.edit', compact('seo', 'article', 'working_dir', 'categories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogArticle  $article
     */
    public function update(Request $request, BlogArticle $article)
    {
      $request->validate([
          'title' => 'required|string',
          'blog_category_id' => 'nullable|exists:blog_categories,id',
      ]);

      $params_update = [];
      $params_update['title'] = $request->title;
      $params_update['status'] = $request->status;
      $params_update['blog_category_id'] = $request->blog_category_id;
      $params_update['data_title'] = $request->data_title;
      if(isset($request->data_title['image']['img'])){
        $compressedImages = CompressModule::compressImage($request->data_title['image']['img'], [200,480,768,1200,1920], 1280);
        $params_update['data_title']['image']['size'] = $compressedImages;
      }
      $carousels = $request->data_content ?? [];
      $content_carousels = $article->data_content ?? [];

      if ($carousels) {
        $carousels_prepare = [];
        foreach($carousels as $key => $carousel){
          if(!isset($carousels_prepare[$key])){
            if(is_array($carousel)){
              $carousels_prepare[$key] = [];
            }else{
              $carousels_prepare[$key] = '';
            }
          }
          $slide_i = 1;
          $carousel_prepare = [];
          if(is_array($carousel)){
            foreach($carousel as $slide){
              $carousel_prepare[$slide_i] = $slide;
              $slide_i++;
            }
          }else{
            $carousel_prepare = $carousel;
          }

          if(!empty($carousel_prepare)){
            $carousels_prepare[$key] = $carousel_prepare;
          }
        }
      }
      $content_carousels['_request'] = $carousels_prepare ?? null;
      $params_update['data_content'] = $content_carousels;
      $old = $article->toArray();

      $article->update($params_update);
      if($content_carousels['_request']){
        CompressArticleImagesJob::dispatch($article->id)->onQueue('compressImages');
      }
      BlogArticle::flushQueryCache();
      $article->addLog('Изменена публикация «'.$article->title.'»', null, [
          'old' => $old,
          'new' => $params_update
      ]);
      return redirect()->route('admin.blog.articles.index')->with([
          'success' => 'Публикация успешно сохранена'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogArticle  $article
     */
    public function destroy(BlogArticle $article)
    {
      $article->delete();
      BlogArticle::flushQueryCache();
      return redirect()->route('admin.blog.articles.index')->with([
          'success' => 'Публикация удалена'
      ]);
    }
}
