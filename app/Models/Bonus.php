<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

  public function transactions()
  {
    return $this->hasMany('App\Models\BonusTransaction');
  }
  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  protected $casts = [
      'expired_at' => 'datetime:Y-m-d H:i:s',
  ];

  protected $fillable = [
      'user_id', 'amount', 'expired_at'
  ];
}
