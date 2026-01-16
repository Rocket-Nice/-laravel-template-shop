<?php

namespace App\Models\Traits;

use App\Models\Pickup;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait FilterOrder
{
  public function scopeForReview(Builder $builder){
    $builder->where('id', '>', 288569);
    $builder->where('confirm', 1);
    $builder->where('orders.created_at', '<', now()->subWeeks(2)->format('Y-m-d H:i:s'));
    $products_filter = Product::query()->select('sku')->where('type_id', 1)->pluck('sku')->toArray();
    $builder->whereHas('items', function(Builder $query) use ($products_filter) {
      $query->whereIn('model', $products_filter);
      $query->whereDoesntHave('product', function(Builder $qr) {
        $qr->whereHas('comments', function(Builder $q) {
          $q->where('user_id', auth()->id());
        });
      });
    });
  }
    public function scopeWithoutTest(Builder $builder)
    {
      $builder->where(
          function ($query) {
            $query->where('data_payment->GatewayName', '!=', 'Test')
                ->orWhere('data_payment->GatewayName', null);
            return $query;
          }
      )
          ->where(function($query){
            $query->where('status', '!=', 'test');
            $query->orWhere('status', null);
          });
    }
    public function scopeFiltered(Builder $builder, $request, $city_permissions = '', $pickups = null)
    {
      $builder->where('data->store_coupon', null);
      if (!$pickups){
        $pickups = Pickup::select('id', 'code', 'name', 'params->role as permission')->get();
      }
      if ($request->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime($request->date_from));
        $builder->where('orders.created_at', '>', $date_from);
      }
      if ($request->date_until) {
        $date_until = date('Y-m-d H:i:s', strtotime($request->date_until));
        $builder->where('orders.created_at', '<', $date_until);
      }
      if($request->exceptions && is_array($request->exceptions)){
        foreach ($request->exceptions as $exception) {
          if(isset($exception['date_from']) && $exception['date_from'] && isset($exception['date_until']) && $exception['date_until']){
            $exception['date_from'] = date('Y-m-d H:i:s', strtotime($exception['date_from']));
            $exception['date_until'] = date('Y-m-d H:i:s', strtotime($exception['date_until']));
            $builder->whereNotBetween('orders.created_at', $exception);
          }
        }
      }
      if ($request->date_status) {
        $date_status_from = date('Y-m-d 00:00:00', strtotime($request->date_status));
        $date_status_to = date('Y-m-d 23:59:59', strtotime($request->date_status));
        $builder->where('status_updated_at', '>=', $date_status_from);
        $builder->where('status_updated_at', '<=', $date_status_to);
      }

      if ($request->order_id) {
        if(strpos($request->order_id, ',')){
          $order_ids = explode(',', str_replace(" ", "", $request->order_id));
          $builder->whereIn('id', $order_ids);
        }else{
          $order_id = trim($request->order_id);
          $builder->where('id', $order_id);
        }
      } else {
        if ($request->keyword) {
          $keyword = trim($request->keyword);
          $builder->where(function ($query) use ($keyword) {
            $keyword = mb_strtolower($keyword);
            $query->where(DB::raw('lower(data->"$.form.full_name")'), 'like', '%' . $keyword . '%')
                ->orWhere(DB::raw('lower(data->"$.form.phone")'), 'like', '%' . $keyword . '%')
                ->orWhere(DB::raw('lower(data->"$.form.email")'), 'like', '%' . $keyword . '%');
            return $query;
          });
        }
      }
      if($request->promocode){
        $promocode = mb_strtolower($request->promocode);
        $builder->whereRaw('lower(JSON_UNQUOTE(data->"$.promocode.code")) = ?', [$promocode]);
      }
      if($request->country){
        $builder->where('data_shipping->country_code', $request->country);
      }
      if($request->invoice_id){
        $invoice_id = $request->invoice_id;
        $builder->whereHas('invoices', function (Builder $query) use ($invoice_id) {
          $query->where('id', '=', $invoice_id);
        });
      }
      if($request->user_id){
        $builder->where('user_id', $request->user_id);
      }
      if ($request->shipping) {
        $shipping = $request->shipping;
        if (in_array('pochta_russia', $request->shipping) && !in_array('pochta_world', $request->shipping)) {
          unset($shipping[array_search('pochta_russia', $shipping)]);
          $builder->where(function ($query) {
            $query->where('data_shipping->shipping-code', 'pochta');
            $query->where('data_shipping->country_code', '=', 0);
          });
        }
        if (in_array('pochta_world', $request->shipping) && !in_array('pochta_russia', $request->shipping)) {
          unset($shipping[array_search('pochta_world', $shipping)]);
          $builder->where(function ($query) {
            $query->where('data_shipping->shipping-code', 'pochta');
            $query->where('data_shipping->country_code', '!=', 0);
          });
        }
        if (in_array('pochta_world', $request->shipping) && in_array('pochta_russia', $request->shipping)) {
          $shipping[] = 'pochta';
        }
//        if (auth()->id()==1){
//          dd($shipping);
//        }
        if (!empty($shipping)) {
          $builder->whereIn('data_shipping->shipping-code', $shipping);
        }
//      } elseif (auth()->user()->hasAnyPermission($city_permissions)) { //
//        $pickup_codes = [];
//        foreach ($pickups as $pickup) {
//          if (isset($pickup->params['role']) && auth()->user()->hasRole($pickup->params['role'])) {
//            $pickup_codes[] = $pickup->code;
//          }
//        }
//        $builder->whereIn('data_shipping->shipping-code', $pickup_codes);
      } elseif (!$request->referrers) {
        $pickup_codes = $pickups->where('params->role', '!=', null)->pluck('code')->toArray();
        $builder->whereNotIn('data_shipping->shipping-code', $pickup_codes);
      }
      if ($request->status) {
        $status = $request->status;
        if (!in_array('is_processing', $status)) {
          $builder->whereIn('status', $status);
        } else {
          $builder->where(function ($query) use ($status) {
            $query->whereIn('status', $status)
                ->orWhere('status', null);
            return $query;
          });
        }
      }elseif($request->hide_test){
        $builder->where('status', '!=', 'test');
      }
      if ($request->product) {
        $products_filter = $request->product;
        foreach ($request->product as $filter) {
          $products_filter[] = $filter.'_maygift';
          $products_filter[] = $filter.'_discounted';
        }
//        $products_filter = [];
//        foreach ($request->product as $filter) {
//          $products_filter[] = [
//              'model' => $filter
//          ];
//        }
//      $builder->whereJsonContains('data_cart', $products_filter);
        $builder->whereHas('items', function(Builder $query) use ($products_filter) {
          $query->whereIn('model', $products_filter);
        });
      }

      if ($request->not_product) {
        $products_filter = $request->not_product;
        foreach ($request->not_product as $filter) {
          $products_filter[] = $filter.'_maygift';
          $products_filter[] = $filter.'_discounted';
        }
//        if(auth()->id()==1){
//          dd($products_filter, $request->not_product);
//        }
        $builder->whereDoesntHave('items', function(Builder $query) use ($products_filter) {
          $query->whereIn('model', $products_filter);
        });
//        foreach ($request->not_product as $filter) {
//          $builder->where('data_cart', 'not like', '%' . $filter . '%');
//        }
      }
      if ($request->referrers) {
        $referrers = $request->referrers;
        $builder->whereIn('partner_id', $referrers);
//        if (in_array('ozon', $request->shipping)){
//          $builder->where('data_shipping->ozon-pvz-id', '!=', null);
//        }
      }

      if ($request->has_paid) {
        $builder->where(function($query){
          $query->where('confirm', 0);
          $query->orWhere('status', 'cancelled');
        });
      } else {
        $builder->where('confirm', 1);
        if(!$request->status && !$request->hide_test){
          $builder->where(function($query){
            $query->where('status', '!=', 'cancelled');
            $query->orWhere('status', null);
          });
        }
      }
      if ($request->no_ticket) {
        $builder->doesntHave('tickets');
      }
      if ($request->preorder) {
        $builder->where('data->preorder', true);
      }else{
        $builder->where(function ($query){
          $query->where('data->preorder', false);
          $query->orWhere('data->preorder', null);
        });
      }
      if ($request->has_noticket) {
        $builder->where('data_shipping->ticket', null);
      }
      if ($request->payment_provider) {
        $builder->where('payment_provider', $request->payment_provider);
      }
    }
}
