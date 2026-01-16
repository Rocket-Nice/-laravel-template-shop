<?php

namespace App\Models;

use App\Models\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Voucher extends Model
{
    use HasFactory, Loggable;

  public function order()
  {
    return $this->belongsTo('App\Models\Order');
  }

  protected $casts = [
      'options' => 'array',
      'available_until' => 'datetime:Y-m-d H:i:s',
      'available_from' => 'datetime:Y-m-d H:i:s',
      'used_at' => 'datetime:Y-m-d H:i:s'
  ];

    protected $fillable = [
        'code',
        'type',
        'amount',
        'count',
        'used_at',
        'order_id',
        'available_until',
        'available_from',
        'save_amount',
        'nominal_value',
        'options',
    ];

  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      if (empty($model->nominal_value) && !empty($model->amount)) {
        $model->nominal_value = $model->amount;
      }
    });
  }
}
