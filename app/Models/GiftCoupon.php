<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCoupon extends Model
{
    use HasFactory;

    private $date = '2025-04-25 10:00:00';
    public function getDate(){
      return Carbon::createFromFormat('Y-m-d H:i:s', $this->date);
    }
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

    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'prize_id',
        'order_id',
        'code',
        'data',
    ];
}
