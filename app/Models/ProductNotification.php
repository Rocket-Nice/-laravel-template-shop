<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductNotification extends Model
{
    use HasFactory;

  public function user() {
    return $this->belongsTo('App\Models\User');
  }
  public function product() {
    return $this->belongsTo('App\Models\Product');
  }

  protected $casts = [
      'notice_date' => 'datetime:Y-m-d H:i:s',
      'created_at' => 'datetime:Y-m-d H:i:s',
  ];

    protected $fillable = [
        'was_noticed',
        'notice_date',
        'product_id',
        'user_id',
    ];
}
