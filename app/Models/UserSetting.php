<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

  public function user() {
    return $this->belongsTo('App\Models\User');
  }

    protected $fillable = [
        'user_id',
        'key',
        'value'
    ];
}
