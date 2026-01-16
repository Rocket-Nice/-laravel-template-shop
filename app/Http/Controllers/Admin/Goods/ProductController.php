<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Jobs\CompressImageJob;
use App\Jobs\ExportProductsJob;
use App\Models\Category;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductType;
use App\Models\ExportFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use SafeObject;

class ProductController extends Controller
{
    public function index(Request $request){
      $products = Product::query()->filtered(new SafeObject($request->toArray()));
      $products = $products->orderBy('id', 'desc')->paginate(100);
      $categories = Category::select('id', 'title', 'category_id')->get();
      $product_types = ProductType::all();
      $seo = [
          'title' => 'Все товары'
      ];
      return view('template.admin.products.index', compact('products', 'categories', 'product_types', 'seo'));
    }

    public function create(Request $request){
      $categories = Category::select('id', 'title', 'category_id')->get();
      $product_types = ProductType::all();
      $product = null;
      if($request->copy){
        $product = Product::where('slug', $request->copy)->first();
      }
      $seo = [
          'title' => 'Добавить товар'
      ];
      return view('template.admin.products.create', compact('seo', 'categories', 'product_types', 'product'));
    }

  public function store(Request $request)
  {
    $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
        'sku' => 'required|string',
    ]);
    $slug = translit($request->name);
    if(Product::query()->where('slug', $slug)->exists()){
      $slug .= '-' . Str::random(2);
    }
    $sku_name = mb_strtolower(trim($request->sku));
    $sku = $this->getSku($sku_name);

    $params_store = [
        'name' => $request->name,
        'sku' => $sku_name.'-'.mb_strtolower(Str::random(3)),
        'product_sku_id' => $sku->id,
        'old_price' => $request->old_price,
        'price' => $request->price,
        'volume' => $request->volume,
        'weight' => $request->weight,
        'tnved' => $request->tnved,
        'preorder' => $request->preorder ?? false,
        'hidden' => $request->hidden ?? false,
        'quantity' => 0,
        'status' => 0,
        'category_id' => $request->category_id,
        'type_id' => $request->type_id,
        'slug' => $slug,
    ];
    $pickup_points = Pickup::query()->select('params->status as status', 'params->quantity as quantity')->get();
    $quantity_data = [];
    $status_data = [];
    foreach($pickup_points as $pickup_point){
      if($pickup_point->quantity){
        $quantity_data[$pickup_point->quantity] = 0;
      }
      if($pickup_point->status){
        $status_data[$pickup_point->status] = 0;
      }
    }
    $params_store['data_quantity'] = $quantity_data;
    $params_store['data_status'] = $status_data;
    if($request->copy){
      $copy_product = Product::find($request->copy);
      if($copy_product){
        $params_store['style_cards'] = $copy_product->style_cards;
        $params_store['style_page'] = $copy_product->style_page;
      }
    }
    $product = Product::create($params_store);

    $categories = $request->categories ?? [];
    if($request->category_id){
      $parents = Category::getAllParentIds(Category::find($request->category_id));
      $categories = array_merge($parents, $categories);
      if (!empty($categories)){
        $product->categories()->sync($categories);
      }else{
        $product->categories()->detach();
      }
    }
    $product->addLog('Создан товар «'.$product->name.'»');
    Product::flushQueryCache();
    return redirect()->route('admin.products.index')->with([
        'success' => 'Товар успешно добавлен'
    ]);
  }

    public function edit(Product $product){
      $categories = Category::select('id', 'title', 'category_id')->get();
      $product_types = ProductType::all();
      $product_id = $product->id;
      $productsOption = Product::select('id', 'sku', 'name')->where('type_id', 5)
          ->where(function($query) use ($product_id){
            $query->where('product_id', null);
            $query->orWhere('product_id', $product_id);
          })->get();
      $mainProducts = Product::query()
          ->select('id', 'slug', 'name')
          ->where('type_id', 1)
          ->get();
      $seo = [
          'title' => 'Редактировать товар'
      ];
      return view('template.admin.products.edit', compact('seo', 'categories', 'product_types', 'product', 'productsOption', 'mainProducts'));
    }
    public function editDesign(Product $product){
      $seo = [
          'title' => 'Дизайн товара «'.$product->name.'»'
      ];
      Product::flushQueryCache();
      $working_dir = '/shares/products/'.$product->sku;
      if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
        mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
      }
      $products = Product::leftJoin('categories', 'products.category_id', '=', 'categories.id')
          ->select('products.slug', 'products.id', 'products.sku', 'products.name', 'products.style_cards', 'products.style_page', 'categories.title as category_title')
          ->where('type_id', 1)
          ->get();
      $cards = null;
      if(request()->cards){
        $product_cards = Product::select('style_cards', 'style_page')->where('slug', request()->cards)->first();
        $cards = $product_cards;
      }
      $design = null;
      if(request()->design){
        $product_design = Product::select('style_page')->where('slug', request()->design)->first();
        $design = $product_design;
      }
      return view('template.admin.products.edit_design', compact('seo', 'product', 'working_dir', 'products', 'cards', 'design'));
    }

  public function update(Product $product, Request $request)
  {
    $request->validate([
        'name' => 'required|string',
        'price' => 'required|numeric',
    ]);
    $product_options = $request->product_options ?? [];
    // обновляем данные в опциях
    if(isset($product_options['productSize'])&&!empty($product_options['productSize'])){
      foreach($product_options['productSize'] as $option_data){
        if(!isset($option_data['product'])||!$option_data['product']){
          continue;
        }
        $product_size = Product::find($option_data['product']);

        if($product_size){
          $product_size->update([
              'old_price' => $request->old_price,
              'price' => $request->price,
              //'volume' => $request->volume,
              //'weight' => $request->weight,
              'product_id' => $product->id,
              'style_page' => $product->style_page,
          ]);
        }
      }
    }else{
      unset($product_options['productSize']);
    }
    $options = $product->options ?? [];
    $options['only_pickup'] = $request->options['only_pickup'] ?? 0;
    $options['gold_coupon'] = $request->options['gold_coupon'] ?? 0;
    $options['new_structure'] = $request->options['new_structure'] ?? 0;
    $options['puzzles'] = $request->options['puzzles'] ?? 0;
    $options['puzzles_count'] = $request->options['puzzles_count'] ?? 0;
    $options['soon'] = $request->options['soon'] ?? 0;
    $options['is_new'] = $request->options['is_new'] ?? 0;
    $options['sale'] = $request->options['sale'] ?? 0;
    $options['tag20'] = $request->options['tag20'] ?? 0;
    $options['tag30'] = $request->options['tag30'] ?? 0;
    $options['tag50'] = $request->options['tag50'] ?? 0;

    $sku_name = mb_strtolower(trim($request->sku));
    $sku = $this->getSku($sku_name);

    $params_update = [
        'name' => $request->name,
        'product_sku_id' => $sku->id,
        'old_price' => $request->old_price,
        'price' => $request->price,
        'volume' => $request->volume,
        'weight' => $request->weight,
        'tnved' => $request->tnved,
        'product_options' => $product_options,
        'category_id' => $request->category_id,
        'type_id' => $request->type_id,
        'main_product_id' => $request->main_product_id ?? null,
        'preorder' => $request->preorder ?? false,
        'hidden' => $request->hidden ?? false,
        'keywords' => $request->keywords,
        'order' => $request->order,
        'options' => $options,
    ];
    $old = $product->toArray();
    $product->update($params_update);

    $product->cleanKeywords();
    $categories = $request->categories ?? [];
    if($request->category_id){
      $parents = Category::getAllParentIds(Category::find($request->category_id));
      $categories = array_merge($parents, $categories);
      if (!empty($categories)){
        $product->categories()->sync($categories);
      }else{
        $product->categories()->detach();
      }
    }


    $product->addLog('Изменены данные товара «'.$product->name.'»', null, [
        'old' => $old,
        'new' => $product->toArray()
    ]);
    Product::flushQueryCache();
    return redirect()->route('admin.products.index')->with([
        'success' => 'Товар успешно обновлен'
    ]);
  }

  public function updateDesign(Product $product, Request $request)
  {
    $request->validate([
        'product_cards' => 'array|nullable'
    ]);
    if($request->clearCache){
      $working_dir = '/shares/products/'.$product->sku.'/compressed/';
      if (file_exists(storage_path('app/public/photos'.$working_dir))) {
        deleteDirectory(storage_path('app/public/photos'.$working_dir));
      }
    }
    $product_cards = $product->style_cards ?? [];
    $request_cards = $request->product_cards ?? [];
    $card_i = 1;

    $product_cards_prepare = [];
    foreach($request_cards as $card){
      $product_cards_prepare[$card_i] = $card;
      $card_i++;
    }
    if(!empty($product_cards_prepare)){
      $request_cards = $product_cards_prepare;
    }
    $product_cards['_request'] =$request_cards ?? [];
    $product_page = $product->style_page ?? [];
    $product_page['_request'] = $request->style_page ?? [];

    $product->update([
        'style_cards' => $product_cards,
        'style_page' => $product_page
    ]);
    Product::flushQueryCache();
    CompressImageJob::dispatch($product->id)->onQueue('compressImages');
    return redirect()->route('admin.products.editDesign', $product->slug)->with([
        'success' => 'Товар успешно обновлен'
    ]);
  }

  public function videoConversion($open, $save, $product_id){
    $product = Product::select('id', 'style_page')->where('id', $product_id)->first();

    $extension = pathinfo($save, PATHINFO_EXTENSION);
    echo $extension.'<br/>';
    $ffmpeg = FFMpeg::fromDisk('public')->open($open);

    if($extension == 'mp4'){
      $format = new \FFMpeg\Format\Video\X264();
      $format->setKiloBitrate(4000); // Установите битрейт видео
      $ffmpeg->addFilter('-an') // Этот фильтр удаляет аудио
      ->addFilter('-preset', 'veryslow')
          ->addFilter('-crf', '18');
    }elseif($extension == 'webm'){
      $format = new \FFMpeg\Format\Video\WebM();
      $format->setVideoCodec('libvpx')
          ->setKiloBitrate(4000);;
    }else{
      return false;
    }

    $ffmpeg->export()
        ->toDisk('public') // также замените 'disk_name', если вы хотите сохранить файл не в локальной файловой системе
        ->inFormat($format)
        ->save($save);


    $style_page = $product->style_page;
    $style_page['mainVideo'][$extension] = asset('storage'.$save);
//    echo asset('storage'.$save).'<br/>';
//    echo '<pre>';
//    print_r($style_page['mainVideo']);
//    echo '</pre>';
    $product->update([
        'style_page' => $style_page
    ]);
    Product::flushQueryCache();
    return true;
  }
  private function getSku($sku_name){
    $sku = ProductSku::query()->where('name', $sku_name)->first();
    if(!$sku){
      $sku = ProductSku::create([
          'name' => $sku_name
      ]);
    }
    return $sku;
  }
  public function updateViewers()
  {
    $products = Product::query()
        ->select('id', 'options')
        ->where('quantity', '>', 0)
        ->where('status', true)
        ->where('type_id', 1)
        ->get();

    foreach($products as $product){
      $options = $product->options;
      $viewers = getRandomNumber($options['viewers'] ?? null);
      $options['viewers'] = $viewers;
      $product->update([
          'options' => $options
      ]);
    }
    Product::flushQueryCache();
  }
  public function marketplaces(Request $request)
  {
    $products = Product::query()->filtered(new SafeObject($request->toArray()));
    $products = $products->orderBy('category_id', 'desc')->orderBy('created_at', 'desc')->paginate(100);
    $categories = Category::select('id', 'title', 'category_id')->where('options->menu', 1)->orderBy('order')->get();
    $product_types = ProductType::all();
    $seo = [
        'title' => 'Все товары'
    ];
    return view('template.admin.products.marketplaces', compact('products', 'categories', 'product_types', 'seo'));
  }
  public function marketplacesSave(Request $request)
  {
    $request->validate([
        'products' => 'array'
    ]);
    $products_data = $request->products;
    $products = Product::query()->select('id', 'is_producing', 'in_stock_wb', 'in_stock_ozon')->whereIn('id', array_keys($products_data))->get();
    foreach($products as $product){
      $product_update = [];
      foreach($products_data[$product->id] as $field => $value){
        if($field == 'is_producing'){
          $product_update[$field] = $products_data[$product->id][$field] == '1';
        }else{
          $product_update[$field] = $value;
        }

      }
//      if(isset($products_data[$product->id]['is_producing']) && ($products_data[$product->id]['is_producing'] === '0' || $products_data[$product->id]['is_producing'])){
//        $product_update['is_producing'] = $products_data[$product->id]['is_producing'] == '1';
//      }
//      if(isset($products_data[$product->id]['in_stock_wb'])){
//        $product_update['in_stock_wb'] = $products_data[$product->id]['in_stock_wb'];
//      }
//      if(isset($products_data[$product->id]['in_stock_ozon'])){
//        $product_update['in_stock_ozon'] = $products_data[$product->id]['in_stock_ozon'];
//      }
//      if(isset($products_data[$product->id]['comment'])){
//        $product_update['comment'] = $products_data[$product->id]['comment'];
//      }
      if(!empty($product_update)){
        $product->update($product_update);
      }
    }
    Product::flushQueryCache();
    return back()->with([
        'Данные успешно сохранены'
    ]);
  }

  public function export(Request $request){
    ExportProductsJob::dispatch($request->toArray(), 1, auth()->id())->onQueue('export_products');
    return back()->with([
        'success' => 'Задача на экспорт продуктов создана'
    ]);
  }

  public function export_job($request, $user_id): void
  {
    $export = new ProductsExport($request);
    $file_name = 'products_'.now()->format('d-m-Y_H-i').'.xlsx';
    $file_path = 'public/export/products/'.$file_name;
    if (!file_exists(storage_path('app/public/export/products'))) {
      mkdir(storage_path('app/public/export/products'), 0777, true);
    }
    $count = \App\Models\Product::query()->select('id')->filtered(new SafeObject($request))->count();
    ExportFile::create([
        'name' => $file_name,
        'path' => $file_path,
        'type' ,
        'lines_count' => $count,
        'exported_by' => $user_id,
    ]);
    Excel::store($export, $file_path);
  }
}
