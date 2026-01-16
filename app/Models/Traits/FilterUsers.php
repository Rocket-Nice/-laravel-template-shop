<?php

namespace App\Models\Traits;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait FilterUsers
{
  public function scopeFilter(Builder $builder, $request){
    if ($request->date_from) {
      $date_from = date('Y-m-d H:i:s', strtotime($request->date_from));
      $builder->where('users.created_at', '>', $date_from);
    }
    if ($request->date_until) {
      $date_until = date('Y-m-d H:i:s', strtotime($request->date_until));
      $builder->where('users.created_at', '<', $date_until);
    }
    if($request->name){
      $builder->where(DB::raw('lower(name)'), 'like', '%'.trim($request->name).'%');
    }
    if($request->email){
      $builder->where(DB::raw('lower(email)'), 'like', '%'.trim($request->email).'%');
    }
    if($request->phone){
      $builder->where(DB::raw('lower(phone)'), 'like', '%'.trim($request->phone).'%');
    }
    $roles = Role::whereHas('permissions', function ($query) {
      $query->where('name', 'Доступ к админпанели');
    })->pluck('name')->toArray();
    if(!auth()->user()->hasPermissionTo('Выгрузка прав пользователей') || $request->is_admin === "0"){
      $builder->where(function($query) use ($roles){
        $query->whereDoesntHave('roles', function ($query) use ($roles) {
          $query->whereIn('name', $roles);
        })->whereDoesntHave('permissions', function ($query) {
          $query->where('name', 'Доступ к админпанели');
        });
      });
    }elseif(auth()->user()->hasPermissionTo('Выгрузка прав пользователей') && $request->is_admin === "1"){
      $builder->where(function($query) use ($roles){
        $query->whereHas('roles', function ($query) use ($roles) {
          $query->whereIn('name', $roles);
        })
            ->orWhereHas('permissions', function ($query) {
              $query->where('name', 'Доступ к админпанели');
            });
      });
    }
//    if($request->no_managers||!auth()->check()||!auth()->user()->hasRole('admin')) {
//      $roles = Role::whereHas('permissions', function ($query) {
//        $query->where('name', 'Доступ к админпанели');
//      })->pluck('name')->toArray();
//
//      $builder->whereDoesntHave('roles', function ($query) use ($roles) {
//        $query->whereIn('name', $roles);
//      });
//    }
    if($request->mailing_list){
      $mailing_list = $request->mailing_list;
      $builder->whereHas('mailing_list', function (Builder $query) use ($mailing_list) {
        $query->whereIn('id', $mailing_list);
      });
    }
    if($request->is_subscribed_to_marketing){
      $builder->where('is_subscribed_to_marketing', true);
    }elseif($request->is_denied_to_marketing){
      $builder->where('is_subscribed_to_marketing', false);
    }
//    else{
//      $builder->where('is_subscribed_to_marketing', false);
//    }
    if($request->not_mailing_list){
      $mailing_list = $request->not_mailing_list;
      $builder->whereDoesntHave('mailing_list', function (Builder $query) use ($mailing_list) {
        $query->whereIn('id', $mailing_list);
      });
    }
    if($request->country||
        $request->region||
        $request->city||
        $request->lastOrderDateFrom||
        $request->lastOrderDateTo||
        $request->minAvgOrderTotal||
        $request->maxAvgOrderTotal||
        $request->hasConfirmedOrder||
        $request->hasRefundedOrder||
        $request->has_children
    ){
      $ordersAvg = Order::select('user_id', DB::raw('AVG(json_unquote(json_extract(data, "$.total"))) as avg_total'))
          ->groupBy('user_id');

      $builder->leftJoinSub($ordersAvg, 'orders_avg', function ($join) {
        $join->on('users.id', '=', 'orders_avg.user_id');
      });

      $country = $request->country;
      $region = $request->region;
      $cities = $request->cities;
      $lastOrderDateFrom = $request->lastOrderDateFrom;
      $lastOrderDateTo = $request->lastOrderDateTo;
      $minAvgOrderTotal = $request->minAvgOrderTotal;
      $maxAvgOrderTotal = $request->maxAvgOrderTotal;
      $hasConfirmedOrder = $request->hasConfirmedOrder;
      $hasRefundedOrder = $request->hasRefundedOrder;
      $has_children = $request->has_children;

      $builder->whereHas('orders', function (Builder $query) use (
          $country,
          $region,
          $cities,
          $lastOrderDateFrom,
          $lastOrderDateTo,
          $hasConfirmedOrder,
          $hasRefundedOrder,
          $has_children
      ) {
        if($hasConfirmedOrder == 'y'){
          $query->where('confirm', 1);
        }elseif($hasConfirmedOrder == 'n'){
          $query->where('confirm', 0);
        }
        if($hasRefundedOrder == 'y'){
          $query->where('status',  'refund');
        }elseif($hasRefundedOrder == 'n'){
          $query->where('status', '!=',  'refund');
        }

        if($country){
          $query->where('country_id', $country);
        }
        if($region){
          $query->where('region_id', $region);
        }
        if($cities && !empty($cities)){
          $query->whereIn('city_id', $cities);
        }
        if($lastOrderDateFrom){
          $lastOrderDateFrom = Carbon::createFromFormat('d.m.Y H:i', $lastOrderDateFrom)->format('Y-m-d H:i:s');
          $query->where('created_at', '>=', $lastOrderDateFrom);
        }
        if($lastOrderDateTo){
          $lastOrderDateTo = Carbon::createFromFormat('d.m.Y H:i', $lastOrderDateTo)->format('Y-m-d H:i:s');
          $query->where('created_at', '<=', $lastOrderDateTo);
        }
        if($has_children){
          $products = Product::query()->where('category_id', 28)->pluck('sku')->toArray();
          $products_filter = $products;
          foreach ($products as $filter) {
            $products_filter[] = $filter.'_maygift';
            $products_filter[] = $filter.'_discounted';
          }

          $query->whereHas('items', function(Builder $q) use ($products_filter) {
            $q->whereIn('model', $products_filter);
          });
        }
      });
      $happy_coupon = $request->happy_coupon;
      if($happy_coupon){
        if($happy_coupon == 'n'){
          $builder->whereDoesntHave('giftCoupons', function (Builder $query) use (
              $lastOrderDateFrom,
              $lastOrderDateTo,
          ) {
            if($lastOrderDateFrom){
              $lastOrderDateFrom = Carbon::createFromFormat('d.m.Y H:i', $lastOrderDateFrom)->format('Y-m-d H:i:s');
              $query->where('created_at', '>=', $lastOrderDateFrom);
            }
            if($lastOrderDateTo){
              $lastOrderDateTo = Carbon::createFromFormat('d.m.Y H:i', $lastOrderDateTo)->format('Y-m-d H:i:s');
              $query->where('created_at', '<=', $lastOrderDateTo);
            }
          });
        }elseif($happy_coupon == 'y'){
          $builder->whereHas('giftCoupons', function (Builder $query) use (
              $lastOrderDateFrom,
              $lastOrderDateTo,
          ) {
            if($lastOrderDateFrom){
              $lastOrderDateFrom = Carbon::createFromFormat('d.m.Y H:i', $lastOrderDateFrom)->format('Y-m-d H:i:s');
              $query->where('created_at', '>=', $lastOrderDateFrom);
            }
            if($lastOrderDateTo){
              $lastOrderDateTo = Carbon::createFromFormat('d.m.Y H:i', $lastOrderDateTo)->format('Y-m-d H:i:s');
              $query->where('created_at', '<=', $lastOrderDateTo);
            }
          });
        }
      }
      $tg_notices = $request->tg_notices;
      if($tg_notices){
        if($tg_notices == 'n'){
          $builder->whereDoesntHave('tgChats', function (Builder $query) {
            $query->where('active', true);
          });
        }elseif($tg_notices == 'y'){
          $builder->whereHas('tgChats', function (Builder $query) {
            $query->where('active', true);
          });
        }
      }
      // Фильтрация по средней стоимости заказов
      if($minAvgOrderTotal) {
        $builder->where('orders_avg.avg_total', '>=', $minAvgOrderTotal);
      }
      if($maxAvgOrderTotal) {
        $builder->where('orders_avg.avg_total', '<=', $maxAvgOrderTotal);
      }
    }
  }
}
