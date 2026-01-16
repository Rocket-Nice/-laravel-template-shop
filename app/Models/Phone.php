<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    use HasFactory;

  public function user() {
    return $this->belongsTo('App\Models\User');
  }

  protected $casts = [
      'data' => 'array'
  ];

    protected $fillable = [
        'user_id',
        'number',
        'ip',
        'verification_code',
        'confirmed',
        'data'
    ];
}
