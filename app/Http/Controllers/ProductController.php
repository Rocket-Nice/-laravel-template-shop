<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Content;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProductController extends Controller
{
    public function index(Product $product){
      if(($product->hidden||$product->type_id!=1) && (!auth()->check() || !auth()->user()->hasPermissionTo('Доступ к админпанели'))){
        return redirect()->route('page.index');
      }
      $product->promoQuantity = $product->data_quantity['promotion'] ?? null;
      $product->promoStatus = $product->data_status['promotion'] ?? null;
      $product->gold_coupon = $product->options['gold_coupon'] ?? null;
      $stockStatus = getStockStatus($product);
      $product->stockStatus = $stockStatus;
      $products = null;
      if(isset($product->style_page['weRecommend'])){
        $products = Product::catalog()->whereIn('id', $product->style_page['weRecommend'])->get();
        $products->map(function ($product) {
          $stockStatus = getStockStatus($product);
          $product->stockStatus = $stockStatus;
          return $product;
        });
      }
      $reviews = $product->comments()
          ->join('users', 'comments.user_id', '=', 'users.id')
          ->select(
              'comments.id',
              'comments.rating',
              'comments.text',
              'comments.created_at',
              'comments.files',
              'users.first_name',
              'users.middle_name',
              'users.last_name',
              'users.img',
              'comments.created_at as created_at'
          )
          ->where('comments.hidden', false)
          ->limit(50)
          ->orderBy('comments.created_at', 'desc')
          ->paginate(10, ['*'], 'page', 1);
      $hasReview = false;
      if(auth()->check()){
        $user = auth()->user();
        $hasReview = $product->comments()->where('user_id', $user->id)->exists();
      }
      $seo = [
          'title' => $product->name
      ];
      return view('template.public.product.index', compact('product', 'products', 'reviews', 'seo', 'hasReview'));
    }
    public function present(Product $product){
      if($product->hidden||$product->type_id!=8){
        return redirect()->route('page.index');
      }
      $products = null;
      if(isset($product->style_page['weRecommend'])){
        $products = Product::catalog()->whereIn('id', $product->style_page['weRecommend'])->get();
      }

      $seo = [
          'title' => $product->name
      ];
      return view('template.public.product.present', compact('product', 'products', 'seo'));
    }
    public function reviews(Product $product)
    {
      $reviews = $product->comments()
          ->join('users', 'comments.user_id', '=', 'users.id')
          ->select(
              'comments.id',
              'comments.rating',
              'comments.text',
              'comments.created_at',
              'comments.files',
              'users.first_name',
              'users.middle_name',
              'users.last_name',
              'users.img',
              'comments.created_at as created_at'
          )
          ->where('comments.hidden', false)
          ->limit(50)
          ->orderBy('comments.created_at', 'desc')
          ->paginate(50);

      $seo = [
          'title' => 'Отзывы '.$product->name
      ];
      return view('template.public.product.reviews', compact('product', 'reviews', 'seo'));
    }
    public function category(Category $category){
      $products = Product::catalog($category->id);
//      $minPrice = $products->min('price');
//      $maxPrice = $products->max('price');
      $minPrice = 0;
      $maxPrice = 0;

//      $products = $products->orderBy('order', 'desc')->orderByDesc('quantity')->orderByDesc('status');

      $products = $products->paginate(30);
      $products->map(function ($product) {
        $stockStatus = getStockStatus($product);
        $product->stockStatus = $stockStatus;
        return $product;
      });
      if(auth()->check()){
        $products->map(function ($product) {
          $product->notification = $product->product_notifications->isNotEmpty();
          unset($product->product_notifications); // Если вам не нужны детали уведомлений
          return $product;
        });
      }
      $content = Content::find(10);
      if (!$content||!$content->active){
        abort(404);
      }
      $seo = [
          'title' => $category->title
      ];
      return view('template.public.product.catalog', compact('products', 'category', 'content', 'seo', 'minPrice', 'maxPrice'));
    }
    public function catalog(){
      $products = Product::query()->catalog();
//      $minPrice = $products->min('price');
//      $maxPrice = $products->max('price');
      $minPrice = 0;
      $maxPrice = 0;

      $orderedCategories = [11, 30, 1, 29, 27, 28];
      $orderedIds = implode(',', $orderedCategories);

      if(request()->search){
        $products = $products
            ->paginate(30);
      }else{
        $products = $products
            ->orderByRaw("FIELD(category_id, $orderedIds)")
            ->orderBy('order', 'desc')
            ->orderBy('status', 'desc')
            ->paginate(30);
      }


      $products->map(function ($product) {
        $stockStatus = getStockStatus($product);
        $product->stockStatus = $stockStatus;
        return $product;
      });
      if(auth()->check()){
        $products->map(function ($product) {
          $product->notification = $product->product_notifications->isNotEmpty();
          unset($product->product_notifications); // Если вам не нужны детали уведомлений
          return $product;
        });
      }
      $content = Content::find(10);
      if (!$content||!$content->active){
        abort(404);
      }
      $seo = [
          'title' => 'Каталог'
      ];
      if(request()->in_stock == 'on'){
        $seo = [
            'title' => 'В наличии'
        ];
      }
      return view('template.public.product.catalog', compact('products', 'content', 'seo', 'minPrice', 'maxPrice'));
    }
    public function presents(){
      $products = Product::query();
      $products->select('products.id', 'name', 'sku', 'volume', 'style_cards', 'style_page->cardImage as cardImage', 'slug', 'hidden');
      // $products->whereNotNull('style_cards');
      $products->where('hidden', false);
      $products->where('type_id', 8);
      $products = $products->paginate(30);

      $content = Content::find(15);
      if (!$content||!$content->active){
        abort(404);
      }
      $seo = [
          'title' => 'Наши презенты'
      ];
      return view('template.public.product.presents', compact('products', 'content', 'seo'));
    }

    public function loadProducts(Request $request){
      $page = $request->page ?? 1;
      $limit = $request->limit ?? 30;
      if ($request->getTotal){
        $products = Product::catalog()->count();
        $result = [
            'total' => $products
        ];
      }else{
        $products = Product::catalog();
//        $minPrice = $products->min('price');
//        $maxPrice = $products->max('price');

        $orderedCategories = [11, 30, 1, 29, 27, 28];
        $orderedIds = implode(',', $orderedCategories);
//        Log::debug($products->toSql());
        $products = $products
//            ->orderByRaw("FIELD(category_id, $orderedIds)")
//            ->orderBy('order', 'desc')
//            ->orderBy('quantity', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $products->map(function ($product) {
          $stockStatus = getStockStatus($product);
          $product->stockStatus = $stockStatus;
          return $product;
        });

        if(auth()->check()){
          $products->map(function ($product) {
            $product->notification = $product->product_notifications->isNotEmpty();
            unset($product->product_notifications);
            return $product;
          });
        }
        $result = $products->toArray();
      }

      return $result;
    }

  public function get(Request $request)
  {
    $products = Product::query();
    if($request->ids){
      $products->whereIn('id', $request->ids);
    }else{
      $products->catalog();
    }
    if($request->category_id){
      $category_id = $request->category_id;
      $products->where(function($query) use ($category_id) {
        $query->where('category_id', $category_id)
            ->orWhereHas('categories', function($q) use ($category_id) {
              $q->where('categories.id', $category_id);
            });
      });
    }
//    if($request->search){
//      $keywords = explode(' ', $request->search);
//      foreach($keywords as $key => $keyword){
//        $keywords[$key] = clean_search_string($keyword);
//      }
//      $keywords = implode(' ', $keywords);
//      $products->search($keywords);
//    }
    if($request->get('in_stock')){
      $products->where('quantity', '>', 0);
      $products->where('status', true);
    }
    if($request->ids){
      $products->orderByRaw('FIELD(id, ' . implode(',', $request->ids) . ')');
    }else{
      $products->orderBy('order', 'desc')->orderByDesc('quantity')->orderByDesc('status');
    }
    $products = $products->paginate(10);


    $product_attributes = [
        'id', 'puzzles_count', 'image', 'route', 'puzzle_color', 'style_cards', 'name', 'subtitle', 'only_pickup', 'print_old_price', 'print_price', 'volume', 'cardsDescription', 'cardsDescriptionIcons', 'button', 'refill_img', 'refill_price', 'refill_button'
    ];
    $products = $products->getCollection()->map(function ($product) use ($product_attributes) {
      if(!getSettings('puzzlesStatus')){
        $product->puzzles_count = false;
      }else{
        $product->puzzle_color = $product->puzzle_color ?? '#6C715C';
      }
      if(isset($product->cardImage['image'])){
        $product->image = generatePictureHtmlCached($product->cardImage, $product->name);
      }
      if($product->style_cards){
        $style_cards = [];
        foreach($product->style_cards as $key => $card){
          if(!isset($card['card_style'])||!isset($card['image'])){
            continue;
          }
          $card['id'] = $key;
          $card['image'] = generatePictureHtmlCached($card, $product->name);
          $style_cards[] = $card;
        }
        $product->style_cards = $style_cards;
      }
      $price = $product->getPrice();
      $product->current_price = $price;
      $product->print_price = formatPrice($price, true);
      if(($product->old_price && $product->old_price > $price) || $price < $product->price){
        $product->print_old_price = formatPrice($product->old_price ?? $product->price, true);
      }
      $product->in_stock = $product->getStock();

      $product->has_options = false;
      if(isset($product->product_options['productSize'])&&!empty($product->product_options['productSize'])){
        $product->has_options = true;
      }
      if($product->in_stock){
        if($product->has_options){
          $button = View::make('components.public.product.to-options-button', ['route' => $product->route])->render();
        }else if($product->preorder) {
          $button = View::make('components.public.product.preorder-button', ['id' => $product->id])->render();
        }else{
          $button = View::make('components.public.product.to-cart-button', ['id' => $product->id])->render();
        }
      }else{
        $button = View::make('components.public.product.notification-button', ['slug' => $product->slug, 'notification' => $product->notification])->render();
      }
      $product->new_structure = (bool)$product->new_structure;
      $product->soon = (bool)$product->soon;
      $product->refill_img = null;
      $product->refill_price = null;
      $product->refill_button = null;
      if($product->refill && isset($product->refill->style_page['cardImage'])){
        $r_img = $product->refill->style_page['cardImage'];
        $product->refill_img = generatePictureHtmlCached($r_img, $product->refill->name.'!');

        $product->refill_price = formatPrice($product->refill->price, true);;
        $product->refill_button = View::make('components.public.product.to-cart-button', ['id' => $product->refill->id])->render();
      }
      $product->button = $button;
      foreach ($product_attributes as $key) {
        if (!isset($product->$key) || !$product->$key || $product->$key == 'null') {
          $product->setAttribute($key, 0);
        }
      }
      $product->route = '';
      if($product->type_id == 1){
        $product->route = route('product.index', $product->slug);
      }
      $stockStatus = getStockStatus($product);
      $product->stockStatus = $stockStatus;
      return $product;
    });
    return response()->json(['data' => $products]);
  }
}
