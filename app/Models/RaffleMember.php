<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleMember extends Model
{
  use HasFactory;

  public function prize()
  {
    return $this->belongsTo('App\Models\Prize');
  }
  public function order()
  {
    return $this->belongsTo('App\Models\Order');
  }
  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
  public function getPrizeCode(){
    $prize_code = null;
    if (isset($this->data['prize_code'])){
      $prize_code = PrizeCode::find($this->data['prize_code']);
    }
    return $prize_code ?? null;
  }

  public function getVoucher(){
    if(!isset($this->data['voucher'])){
      return null;
    }
    $voucher = Voucher::where('code', $this->data['voucher'])->first();
    return $voucher;
  }

  protected $casts = [
      'data' => 'array'
  ];
  protected $fillable = [
      'user_id',
      'code',
      'order_id',
      'prize_id',
      'conditions',
      'data',
      'created_at',
      'count'
  ];
}
