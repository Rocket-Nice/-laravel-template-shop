<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperBonusTransaction extends Model
{
    use HasFactory;


  public $timestamps = false;

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s'
  ];

  protected $fillable = [
      'bonus_id',
      'user_id',
      'amount',
      'comment',
      'created_at',
      'updated_at',
  ];
}
