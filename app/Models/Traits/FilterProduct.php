<?php

namespace App\Models\Traits;

use App\Models\Pickup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

trait FilterProduct
{
    public function scopeFiltered(Builder $builder, $request)
    {
      if($request->keyword){
        $keyword = trim($request->keyword);
        $builder->where(function ($query) use ($keyword) {
          $keyword = mb_strtolower($keyword);
          $query->where(DB::raw('lower(name)'), 'like', '%'.$keyword.'%')
              ->orWhere(DB::raw('lower(sku)'), 'like', '%'.$keyword.'%');
          return $query;
        });
      }
      if($request->sku){
        $sku = trim($request->sku);
        $builder->where(function ($query) use ($sku) {
          $sku = mb_strtolower($sku);
          $query->whereHas('product_sku', function($q) use ($sku) {
            $q->where('name', $sku);
          });
          return $query;
        });
      }
      if($request->main_category){
        $builder->whereIn('category_id', $request->main_category);
      }
      if($request->categories){
        $category_ids = $request->categories;
        $builder->where(function($query) use ($category_ids) {
          $query->whereIn('category_id', $category_ids)
              ->orWhereHas('categories', function($q) use ($category_ids) {
                $q->whereIn('categories.id', $category_ids);
              });
        });
      }
//      if($request->product_type){
//        $builder->where('type_id', $request->product_type);
//      }
      if($request->product_type&&$request->product_type!='{all}'){
        $builder->where('type_id', $request->product_type);
      }elseif(!$request->product_type){
        if(in_array(mb_strtolower(Route::currentRouteName()), ['admin.products.statistic', 'admin.products.quantity', 'admin.product-group.products'])){
          $builder->whereIn('type_id', [1,5,9]);
        }else{
          $builder->where('type_id', 1);
        }
      }

      if($request->stock){
        foreach($request->stock as $field) {
          if ($field != 'quantity') {
            $builder->where('data_quantity->'.$field, '>', 0);
          }else{
            $builder->where('quantity', '>', 0);
          }
        }
      }
      if($request->not_stock){
        foreach($request->not_stock as $field) {
          if ($field != 'quantity') {
            $builder->where(function($query) use ($field) {
              $query->where('data_quantity->'.$field, null);
              $query->orWhere('data_quantity->'.$field, '<=', 0);
            });
          }else{
            $builder->where(function($query){
              $query->where('quantity', null);
              $query->orWhere('quantity', '<=', 0);
            });
          }
        }
      }
      if($request->active){
        foreach($request->active as $field) {
          if ($field != 'status') {
            $builder->where('data_status->'.$field, '>', 0);
          }else{
            $builder->where('status', '>', 0);
          }
        }
      }
      if($request->not_active){
        foreach($request->not_active as $field) {
          if ($field != 'status') {
            $builder->where(function($query) use ($field) {
              $query->where('data_status->'.$field, null);
              $query->orWhere('data_status->'.$field, '<=', 0);
            });
          }else{
            $builder->where(function($query){
              $query->where('status', null);
              $query->orWhere('status', '<=', 0);
            });
          }
        }
      }
    }
}
