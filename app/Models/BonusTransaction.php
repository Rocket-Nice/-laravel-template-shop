<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusTransaction extends Model
{
    use HasFactory;

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }


  public function createdBy()
  {
    return $this->belongsTo('App\Models\User', 'created_by', 'id');
  }

  protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s'
  ];

  protected $fillable = [
      'bonus_id',
      'user_id',
      'amount',
      'comment',
      'created_by',
  ];
}
