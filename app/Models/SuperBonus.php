<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuperBonus extends Model
{
    use HasFactory;

  public function transactions()
  {
    return $this->hasMany('App\Models\SuperBonusTransaction');
  }
  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

  protected $fillable = [
      'user_id', 'amount'
  ];
}
