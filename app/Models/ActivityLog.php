<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

  public function loggable()
  {
    return $this->morphTo();
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
      'loggable_id',
      'loggable_type',
      'action',
      'text',
      'data'
  ];
}
