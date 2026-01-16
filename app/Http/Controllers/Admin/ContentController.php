<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\CompressContentImagesJob;
use App\Jobs\CompressImageJob;
use App\Models\Content;
use App\Models\Product;
use App\Services\CompressModule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Spatie\PdfToImage\Pdf;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $content = Content::paginate(50);
      $seo = [
          'title' => 'Страницы сайта'
      ];
      return view('template.admin.content.index', compact('content', 'seo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Добавить страницу'
      ];
      return view('template.admin.content.create', compact('seo'));
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
          'title' => 'required|string|max:255',
          'route' => 'required|string|max:255|unique:contents,route',
      ]);
      $content = Content::create([
          'title' => $request->title,
          'route' => $request->route,
          'template_path' => $request->template_path,
          'active' => $request->active ?? false,
      ]);
      Content::flushQueryCache();
      $content->addLog('Создан контент «'.$content->title.'»');
      return redirect()->route('admin.content.index')->with([
          'success' => 'Новыя страница создана'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Content $content)
    {
      $working_dir = '/shares/content/'.$content->route;
      if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
        mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
      }
      $files_dir = '/shares/content/'.$content->route;
      if (!file_exists(storage_path('app/public/files'.$working_dir))) {
        mkdir(storage_path('app/public/files'.$working_dir), 0777, true);
      }
      $products = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
          ->select('products.id', 'products.sku', 'products.name', 'categories.title as category_title')
          ->whereIn('type_id', [1,9])
          ->get();

      $seo = [
          'title' => 'Изменить данные страницы'
      ];
      return view('template.admin.content.edit', compact('seo', 'content', 'products', 'working_dir', 'files_dir'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Content $content)
    {
      $request->validate([
          'title' => 'required|string|max:255',
          'route' => 'required|string|max:255',
      ]);
//      if(isset($content->iamge_data['_request'])||isset($content->carousel_data['_request'])){
//        return back()->withErrors(['Есть несохраненные изменения, подождите несколько минут и повторите попытку']);
//      }
      $images = $content->image_data ?? [];
      $images['_request'] = $request->image_data ?? null;
      $carousels = $request->carousel_data ?? [];
      $content_carousels = $content->carousel_data ?? [];

      if ($carousels) {
        $carousels_prepare = [];
        foreach($carousels as $key => $carousel){
          if(!isset($carousels_prepare[$key])){
            $carousels_prepare[$key] = [];
          }
          $orderCarousel = $request->get('order-'.$key);
          if(isset($orderCarousel) && empty(array_diff($carousel, $content->carousel_data[$key] ?? [])) && empty(array_diff($content->carousel_data[$key] ?? [], $carousel))){
            $carousel = array_keys($orderCarousel);
          }
          $slide_i = 1;
          $carousel_prepare = [];
          foreach($carousel as $slide){
            $carousel_prepare[$slide_i] = $slide;
            $slide_i++;
          }
          if(!empty($carousel_prepare)){
            $carousels_prepare[$key] = $carousel_prepare;
          }
        }
      }
      $content_carousels['_request'] = $carousels_prepare ?? null;
      $old = $content->toArray();

      $content_prams = [
          'title' => $request->title,
          'route' => $request->route,
          'template_path' => $request->template_path,
          'active' => $request->active ?? false,
          'text_data' => $request->text_data ?? [],
          'carousel_data' => $content_carousels,
          'image_data' => $images,
          'keywords' => $request->keywords,
      ];
      $content->update($content_prams);
      CompressContentImagesJob::dispatch($content->id)->onQueue('compressImages');
      Content::flushQueryCache();
      $content->addLog('Изменен контент «'.$content->title.'»', null, [
          'old' => $old,
          'new' => $content_prams
      ]);
      return redirect()->route('admin.content.index')->with([
          'success' => 'Страница успешно изменена'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
