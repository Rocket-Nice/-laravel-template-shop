<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referrer extends Model
{
  use HasFactory;

  protected $table = 'referers';
  public function getRouteKeyName()
  {
    return 'slug';
  }

  /*
   * Количество заказов под данным реферером, от даты внедрения скрипта
   */
  public function orders()
  {
    return Order::select('id')->where('created_at', '>', '2023-08-30 18:00:00')->where('data->referer', '!=', null)->where('data->referer', $this->id)->where('confirm', 1)->count();
  }

  public function sum()
  {
    return Order::select('data->total')->where('created_at', '>', '2023-08-30 18:00:00')->where('data->referer', '!=', null)->where('data->referer', $this->id)->where('confirm', 1)->sum('data->total');
  }

  public function getPromo()
  {
    $coupone = Coupone::where('options->seller', $this->id)->first();
    return $coupone;
  }
  public function getUser()
  {
    $user = User::where('options->referrer', $this->id)->first();
    return $user;
  }

  protected $casts = [
      'data' => 'array'
  ];

  protected $fillable = [
      'name',
      'description',
      'data',
      'redirect',
      'slug',
  ];
}
