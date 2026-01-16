<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    public function visits(){
      return $this->hasMany(PageView::class);
    }
  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }
  public function coupone()
  {
    return $this->belongsTo('App\Models\Coupone');
  }

  public function orders() {
    return $this->hasMany('App\Models\Order');
  }

    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'coupone_id',
        'name',
        'description',
        'data',
        'redirect',
        'slug',
    ];
}
