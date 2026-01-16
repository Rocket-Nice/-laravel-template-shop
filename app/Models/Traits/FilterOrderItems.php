<?php

namespace App\Models\Traits;

use App\Models\Pickup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait FilterOrderItems
{
    public function scopeFiltered(Builder $builder)
    {
      if (request()->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
      }else{
        $date_from = now()->startOfMonth()->format('Y-m-d H:i:s');
      }
      $builder->whereHas('order', function(Builder $query) use ($date_from){
        $query->where('created_at', '>', $date_from);
      });
      if (request()->date_to) {
        $date_to = date('Y-m-d H:i:s', strtotime(request()->date_to));
      }else{
        $date_to = now()->format('Y-m-d H:i:s');
      }
      $builder->whereHas('order', function(Builder $query) use ($date_to){
        $query->where('created_at', '<', $date_to);
      });
      $builder->whereHas('order', function(Builder $query) use ($date_to){
        $query->where('confirm', 1);
      });
      $builder->whereDoesntHave('order', function(Builder $query) use ($date_to){
        $query->where(function($query){
          $query->where('data->this_status->status', 'test');
        });
      });
    }
}
