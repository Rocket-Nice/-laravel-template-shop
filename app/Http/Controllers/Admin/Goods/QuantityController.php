<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Pickup;
use App\Models\Product;
use App\Models\ProductNotification;
use App\Models\ProductType;
use App\Services\MailSender;
use App\Services\TelegramSender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SafeObject;

class QuantityController extends Controller
{
    public function index(Request $request)
    {
      $products = Product::select('id', 'name', 'sku','product_sku_id',  'status', 'volume', 'preorder', 'quantity', 'data_quantity', 'data_status', 'product_options')->filtered(new SafeObject($request->toArray()));
      $products = $products->orderBy('id', 'asc')->paginate(200);

      $pickups = Pickup::where('params', '!=', null)->get();
      $categories = Category::has('products')->get();
      $product_types = ProductType::all();
      $seo = [
          'title' => 'Наличие товаров'
      ];
      return view('template.admin.quantity.index', compact('products', 'pickups', 'categories', 'product_types', 'seo'));
    }

    public function statistic(Request $request){
      $products = Product::select('id', 'name', 'sku', 'category_id')->filtered(new SafeObject($request->toArray()));

      if ($request->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime($request->date_from));
      }else{
        $date_from = now()->startOfMonth()->format('Y-m-d H:i:s');
      }
      $products->whereHas('orderItems', function(Builder $builder) use ($date_from){
        $builder->whereHas('order', function(Builder $query) use ($date_from){
          $query->where('created_at', '>', $date_from);
        });
      });
      if ($request->date_to) {
        $date_to = date('Y-m-d H:i:s', strtotime($request->date_to));
      }else{
        $date_to = now()->format('Y-m-d H:i:s');
      }
      $products->whereHas('orderItems', function(Builder $builder) use ($date_to){
        $builder->whereHas('order', function(Builder $query) use ($date_to){
          $query->where('created_at', '<', $date_to);
        });
      });

      $products = $products->orderBy('id', 'asc')->paginate(200);

      $product_ids = $products->pluck('id')->toArray();
      $orderItems = OrderItem::select(DB::raw('SUM(qty) as count'), 'product_id')
          ->filtered()
          ->whereIn('product_id', $product_ids)
          ->groupBy('product_id')
          ->get();
      $orderSoldItems = OrderItem::select(DB::raw('SUM(qty) as count'), DB::raw('MAX(price) as price'), 'product_id')
          ->filtered(true)
          ->whereIn('product_id', $product_ids)
          ->groupBy('product_id')
          ->get();
      $orderGifts = OrderItem::select(DB::raw('SUM(qty) as gifts'), 'product_id')
          ->filtered()
          ->whereIn('product_id', $product_ids)
          ->where('parent_id', null)
          ->where(function($query) {
            $query->where('price', 0)
                ->orWhere('params->raffle', '!=', null);
          })
          ->groupBy('product_id')
          ->get();
      $categories = Category::has('products')->get();
      $product_types = ProductType::all();
      $seo = [
          'title' => 'Статистика товаров'
      ];
      return view('template.admin.quantity.statistic', compact('products', 'categories', 'seo', 'orderItems', 'orderSoldItems', 'orderGifts', 'product_types'));
    }

    public function getStatistic(){
      $product_ids = request()->products;
      $orderItems = OrderItem::select('qty', 'price', 'parent_id', 'name')->filtered()->whereIn('product_id', $product_ids)->paginate(5000);

      $resultStat = [];
      foreach($product_ids as $product_id){
        if(!isset($resultStat[$product_id])){
          $resultStat[$product_id] = [
              'count' => 0,
              'total' => 0,
              'sum' => 0,
          ];
        }
        $resultStat[$product_id]['count'] += $orderItems->where('product_id', $product_id)->where('price', '>', 0)->where('name', 'not like', '%подарок%')->sum('qty');
        $resultStat[$product_id]['total'] += $orderItems->where('product_id', $product_id)->where('price', '>', 0)->where('name', 'not like', '%подарок%')->sum('price');
        $resultStat[$product_id]['sum'] += $orderItems->where('product_id', $product_id)->where('parent_id', null)->where(function($query){
          $query->where('price', 0)->orWhere('name', 'like', '%подарок%');
        })->sum('qty');
      }
      return ['stat' => $resultStat];
    }

    public function update(Request $request){
      $products_to_notification = collect();
      if ($request->quantity){
        $quantity = $request->quantity;
        $products = Product::select('id', 'name', 'quantity', 'status', 'slug')->whereIn('id', array_keys($quantity))->get();
        foreach($products as $product){
          if($product->quantity <= 0 && $quantity[$product->id] > 0) {
            $products_to_notification->push($product);
          }
          $product->update([
              'quantity' => $quantity[$product->id] ?? 0
          ]);
        }
        $products = null;
        $quantity = null;
      }
      if ($request->status){
        $status = $request->status;
        $products = Product::select('id', 'name', 'quantity', 'status', 'slug')->whereIn('id', array_keys($status))->get();
        foreach($products as $product){
          if(!$product->status && $status[$product->id]) {
            $products_to_notification->push($product);
          }
          $product->update([
              'status' => $status[$product->id] ?? false
          ]);
        }
        $products = null;
        $status = null;
      }
      $request_params = $request->toArray();
      DB::transaction(function () use ($request_params) {
        // Обработка data_quantity
        if (isset($request_params['data_quantity'])) {
          $quantities = Pickup::select('params->quantity as quantity')->get()->pluck('quantity')->toArray();
          $quantities[] = 'promotion';
          foreach ($request_params['data_quantity'] as $quantity => $data) {
            if (!in_array($quantity, $quantities)) {
              continue;
            }
            $products = Product::select('id', 'data_quantity')->whereIn('id', array_keys($data))->get();
            foreach ($products as $product) {
              if (!isset($data[$product->id])) {
                continue;
              }
              $data_quantity = $product->data_quantity ?? [];
              if (!isset($data_quantity[$quantity])) {
                $data_quantity[$quantity] = $data[$product->id] ?? 0;
                $escaped_data_quantity = json_encode($data_quantity, JSON_UNESCAPED_UNICODE);
                DB::update("UPDATE `products` SET `data_quantity` = ? WHERE `id` = ? LIMIT 1", [$escaped_data_quantity, $product->id]);
              } else {
                DB::update("UPDATE `products` SET `data_quantity` = JSON_SET(data_quantity, ?, ?) WHERE `id` = ? LIMIT 1", ['$.'.$quantity, $data[$product->id], $product->id]);
              }
            }
          }
        }

        // Обработка data_status
        if (isset($request_params['data_status'])) {
          $statuses = Pickup::select('params->status as status')->get()->pluck('status')->toArray();
          $statuses[] = 'promotion';
          foreach ($request_params['data_status'] as $status => $data) {
            if (!in_array($status, $statuses)) {
              continue;
            }
            $products = Product::select('id', 'data_status')->whereIn('id', array_keys($data))->get();
            foreach ($products as $product) {
              if (!isset($data[$product->id])) {
                continue;
              }
              $data_status = $product->data_status ?? [];
              if (!isset($data_status[$status])) {
                $data_status[$status] = $data[$product->id] ?? false;
                $escaped_data_status = json_encode($data_status, JSON_UNESCAPED_UNICODE);
                DB::update("UPDATE `products` SET `data_status` = ? WHERE `id` = ? LIMIT 1", [$escaped_data_status, $product->id]);
              } else {
                DB::update("UPDATE `products` SET `data_status` = JSON_SET(data_status, ?, ?) WHERE `id` = ? LIMIT 1", ['$.'.$status, $data[$product->id], $product->id]);
              }
            }
          }
        }
      }, 1);

      // обновляем опции
      (new SystemController())->updateQtyOptions();



      Product::flushQueryCache();
      if($products_to_notification->count()){
        foreach($products_to_notification as $product){
          $product_db = Product::select('id', 'name', 'quantity', 'status', 'slug')->where('id', $product->id)->first();
          if(!$product_db->status || $product_db->quantity <= 0){
            continue;
          }
          $notificstions = ProductNotification::query()
              ->where('product_id', $product->id)
              ->where('was_noticed', false)
              ->where('notice_date', null)
              ->get();
          if($notificstions->count()){
            foreach($notificstions as $notificstion){
              (new MailSender($notificstion->user->email))->productNotification($product);
              foreach($notificstion->user->tgChats as $tgChat){
                (new TelegramSender($tgChat))->productNotification($product);
              }
              $notificstion->update([
                  'was_noticed' => true,
                  'notice_date' => now()->format('Y-m-d H:i:s'),
              ]);
            }
          }
        }
      }

      return back()->with([
          'success' => 'Данные обновлены'
      ]);
    }
}
