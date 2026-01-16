<?php

namespace App\Http\Controllers;

use App\Models\BlogArticle;
use App\Models\BlogCategory;
use App\Models\Category;
use App\Models\Content;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Services\PuzzleService\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

class HomeController extends Controller
{
    public function index()
    {
      $content = Content::where('route', Route::currentRouteName())->first();
      if (!$content||!$content->active){
        abort(404);
      }
      $common =  Content::find(9);
      $addData = collect([]);
//      dd($content->carousel_data);
      if(isset($content->carousel_data['weRecommend'])){
        $ids = $content->carousel_data['weRecommend'];
        $weRecommend = Product::catalog()->whereIn('id', $ids)->get();
        $weRecommend->map(function ($product) {
          $stockStatus = getStockStatus($product);
          $product->stockStatus = $stockStatus;
          return $product;
        });
        $weRecommend = $weRecommend->sortBy(function ($item) use ($ids) {
          return array_search($item->id, $ids);
        });
        $addData->weRecommend = $weRecommend;
      }

//      if(isset($content->carousel_data['weRecommend2'])){
//        $ids = $content->carousel_data['weRecommend2'];
//        $weRecommend2 = Product::catalog()->whereIn('id', $ids)->get();
//        $weRecommend2->map(function ($product) {
//          $stockStatus = getStockStatus($product);
//          $product->stockStatus = $stockStatus;
//          return $product;
//        });
//        $weRecommend2 = $weRecommend2->sortBy(function ($item) use ($ids) {
//          return array_search($item->id, $ids);
//        });
//        // Разделяем на доступные и недоступные товары
//        $available = $weRecommend2->filter(function ($item) {
//          return $item->status && $item->quantity > 0;
//        });
//        $unavailable = $weRecommend2->filter(function ($item) {
//          return !($item->status && $item->quantity > 0);
//        });
//
//        // Сортируем каждую группу отдельно
//        $available = $available->sortBy(function ($item) use ($ids) {
//          return array_search($item->id, $ids);
//        });
//        $unavailable = $unavailable->sortBy(function ($item) use ($ids) {
//          return array_search($item->id, $ids);
//        });
//
//        // Объединяем результаты
//        $weRecommend2 = $available->concat($unavailable);
//        $addData->weRecommend2 = $weRecommend2;
//      }
//      if(isset($content->carousel_data['weRecommend2'])){
//        $weRecommend2 = Product::catalog()->whereIn('id', $content->carousel_data['weRecommend2'])->get();
//        $addData->weRecommend2 = $weRecommend2;
//      }
      $articles = null;
      if($content->id==1){
        $articles = BlogArticle::query()->whereIn('status', [1])->orderByDesc('created_at')->paginate(30);
        $novinkiVKategoriiax = ProductGroup::find(2);
        $novinkiVKategoriiax = $novinkiVKategoriiax->products()->catalog(null, false)
            ->where('status', true)
            ->where('quantity', '>', 0)
//            ->orderByRaw('CASE
//              WHEN status = true AND quantity > 0 THEN 0
//              WHEN options->>"$.soon" = true THEN 1
//              ELSE 2
//            END')
            ->orderBy('pivot_order', 'asc')->get();
//        $product_ids = [1007, 1011, 1187, 1188, 1189, 1061, 1062, 1063, 1064, 1029, 1030, 1031, 1206, 1213, 1218, 1167, 1168, 1169, 1166, 1072, 1078, 1076, 1069, 1070, 1294, 1295, 1296, 1297, 1298, 1299, 1300, 1301, 1302, 1303, 1304, 1305, 1205, 1171, 1172, 1174, 1175, 1176, 1177, 1178, 1179];
//        $products = Product::catalog()->whereIn('id', $product_ids)->where('status', 1)->where('quantity', '>', 0)->get();
//        $products = Product::catalog()->whereIn('category_id', [29,33])->where('status', 1)->where('quantity', '>', 0)->get();
        $novinkiVKategoriiax->map(function ($product) {
          $stockStatus = getStockStatus($product);
          $product->stockStatus = $stockStatus;
          return $product;
        });
        $available = $novinkiVKategoriiax->filter(function ($item) {
          return $item->status && $item->quantity > 0;
        });
        $unavailable = $novinkiVKategoriiax->filter(function ($item) {
          return !($item->status && $item->quantity > 0);
        });
        $products = $available->concat($unavailable);
        $addData->weRecommend2 = $products;

        $bestsellers = ProductGroup::find(3);
        $bestsellers = $bestsellers->products()->catalog(null, false)
            ->where('status', true)
            ->where('quantity', '>', 0)
//            ->orderByRaw('CASE
//              WHEN status = true AND quantity > 0 THEN 0
//              WHEN options->>"$.soon" = true THEN 1
//              ELSE 2
//            END')
            ->orderBy('pivot_order', 'asc')->get();
//        $product_ids = [1007, 1011, 1187, 1188, 1189, 1061, 1062, 1063, 1064, 1029, 1030, 1031, 1206, 1213, 1218, 1167, 1168, 1169, 1166, 1072, 1078, 1076, 1069, 1070, 1294, 1295, 1296, 1297, 1298, 1299, 1300, 1301, 1302, 1303, 1304, 1305, 1205, 1171, 1172, 1174, 1175, 1176, 1177, 1178, 1179];
//        $products = Product::catalog()->whereIn('id', $product_ids)->where('status', 1)->where('quantity', '>', 0)->get();
//        $products = Product::catalog()->whereIn('category_id', [29,33])->where('status', 1)->where('quantity', '>', 0)->get();
        $bestsellers->map(function ($product) {
          $stockStatus = getStockStatus($product);
          $product->stockStatus = $stockStatus;
          return $product;
        });
////        // Разделяем на доступные и недоступные товары
        $available = $bestsellers->filter(function ($item) {
          return $item->status && $item->quantity > 0;
        });
        $unavailable = $bestsellers->filter(function ($item) {
          return !($item->status && $item->quantity > 0);
        });

//
//        // Объединяем результаты
        $products = $available->concat($unavailable);
        $addData->vigodno = $products;
        $products2 = Product::catalog()->where('category_id', 32)->orderBy('order', 'desc')->orderByDesc('quantity')->orderByDesc('status')->get();
        $products2->map(function ($product) {
          $stockStatus = getStockStatus($product);
          $product->stockStatus = $stockStatus;
          return $product;
        });
        // Разделяем на доступные и недоступные товары
        $available = $products2->filter(function ($item) {
          return $item->status && $item->quantity > 0;
        });
        $unavailable = $products2->filter(function ($item) {
          return !($item->status && $item->quantity > 0);
        });

        // Сортируем каждую группу отдельно
        if(isset($ids)&&!empty($ids)){
          $available = $available->sortBy(function ($item) use ($ids) {
            return array_search($item->id, $ids);
          });
          $unavailable = $unavailable->sortBy(function ($item) use ($ids) {
            return array_search($item->id, $ids);
          });
        }


        // Объединяем результаты
        $products2 = $available->concat($unavailable);
        $addData->minisets = $products2;

        $categories = Category::catalog()->orderBy('order')->get();

        $addData->categories = $categories;
      }
      return view('template.public.'.$content->route, compact('content', 'common', 'addData', 'articles'));
    }
    public function dermatologists()
    {
      $content = Content::where('route', 'page.dermatologists')->first();
      $common =  Content::find(9);
      return view('template.public.page.dermatologists_all', compact('content', 'common'));
    }
    public function puzzles(){
      $prize = null;
      $puzzleImage = null;
      $puzzleClient = new Client();
      $puzzleClient->getToken();
      if(auth()->check()){
        $user = auth()->user();
        $puzzleImage = $user->puzzleImages()->where('is_correct', true)->first();

        if($puzzleImage && $puzzleImage->member_id){
          $prize = $puzzleClient->prizeByParticipant($puzzleImage->member_id);
          if(!is_array($prize)){
            $prize = null;
          }
        }
      }
      if ($this->isDataValid()) {
        $prizes = Cache::get('prizes_data');
      } else {
        $prizes = $puzzleClient->getPrizes(['limit' => 200]);
        $this->storePrizesDataInSession($prizes);
      }

      $content = Content::where('route', 'page.puzzles')->first();
      $common =  Content::find(9);
      return view('template.public.page.puzzles', compact('content', 'common', 'prize', 'puzzleImage', 'prizes'));
    }
  public function isDataValid()
  {
    if (Cache::has('prizes_data') && Cache::has('prizes_data_timestamp')) {

      $timestamp = Cache::get('prizes_data_timestamp');
      $currentTime = now();

      // Проверяем, прошло ли менее 30 минут с момента получения токена
      return $currentTime->diffInMinutes($timestamp) < 60;
    }
    return false;
  }

  public function storePrizesDataInSession($data)
  {
    // Сохраняем токен и текущую временную метку в сессии
    Cache::put('prizes_data', $data);
    Cache::put('prizes_data_timestamp', now());
  }

  public function blog()
  {
//    if(!auth()->user()->hasPermissionTo('Доступ к админпанели')){
//      abort(404);
//    }
    $categories = BlogCategory::query()->whereIn('status', [1,2])->get();
    $articles = BlogArticle::query()->whereIn('status', [1])->paginate(30);
    $content = Content::find(18);
    $seo = [
        'title' => 'Новости'
    ];
    return view('template.public.blog.index', compact('seo', 'categories', 'articles', 'content'));
  }
  public function link()
  {
    $products = [
        [
            'img' => asset('img/link/product-2.png'),
            'name' => "Сыворотка для&nbsp;восстановления микробиома",
            'link' => "https://www.wildberries.ru/catalog/311896141/detail.aspx?targetUrl=GP&utm_source=TaplinkproductLM&utm_medium=cpc&utm_campaign=1366629-id-Serummicrobiome"
        ],
        [
            'img' => asset('img/link/product-1.png'),
            'name' => "Сыворотка 3D увлажнение",
            'link' => "https://www.wildberries.ru/catalog/322126486/detail.aspx?targetUrl=GP&utm_source=TaplinkproductLM&utm_medium=cpc&utm_campaign=1366629-id-serum3dmoisturizing"
        ],
        [
            'img' => asset('img/link/product-3.png'),
            'name' => "Сыворотка витаминная",
            'link' => "https://www.wildberries.ru/catalog/311893094/detail.aspx?targetUrl=GP&utm_source=TaplinkproductLM&utm_medium=cpc&utm_campaign=1366629-id-serumvitamin"
        ],
        [
            'img' => asset('img/link/product-4.png'),
            'name' => "Тоник увлажняющий",
            'link' => "https://www.wildberries.ru/catalog/311555350/detail.aspx?targetUrl=GP&utm_source=TaplinkproductLM&utm_medium=cpc&utm_campaign=1366629-id-tonicmoisturizing"
        ],

    ];
    return view('link', compact('products'));
  }
  public function blog_category(BlogCategory $category)
  {
    if($category->status != 1&& (!auth()->check() || !auth()->user()->hasPermissionTo('Доступ к админпанели'))){ //
      abort(404);
    }
    $articles = $category->articles()->whereIn('status', [1])->paginate(30);
    $seo = [
        'title' => $category->name
    ];
    return view('template.public.blog.category', compact('seo', 'category', 'articles'));
  }
  public function blog_article(BlogArticle $article)
  {
    if($article->status != 1&& (!auth()->check() || !auth()->user()->hasPermissionTo('Доступ к админпанели'))){ //
      abort(404);
    }
    if(isset($article->data_content['products'])){
      $weRecommend = Product::catalog()->whereIn('id', $article->data_content['products'])->get();

      $article->products = $weRecommend;
    }
    $articles = BlogArticle::query()->whereIn('status', [1])->paginate(30);
    $seo = [
        'title' => strip_tags($article->title)
    ];
    return view('template.public.blog.article', compact('seo', 'article', 'articles'));
  }
  public function apiDocumentation()
  {
    return view('api_documentation');
  }
}
