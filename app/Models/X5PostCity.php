<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class X5PostCity extends Model
{
    use HasFactory;

  public function region() {
    return $this->belongsTo('App\Models\X5PostRegion', 'region_id');
  }

  public function pvzs() {
    return $this->hasMany('App\Models\X5PostPvz', 'city_id');
  }


  protected $fillable = [
      'region_id',
      'name',
      'city_type',
      'lm_country_id',
      'lm_region_id',
      'lm_city_id',
  ];
}
