<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxberryRegion extends Model
{
    use HasFactory;

  public function cities() {
    return $this->hasMany('App\Models\BoxberryCity', 'region_id');
  }

  public function lm_region(){
    return $this->belongsTo('App\Models\Region', 'lm_region_id');
  }

  public function country(){
    return $this->belongsTo('App\Models\Country', 'lm_country_id');
  }

  protected $casts = [
      'data' => 'array'
  ];

  protected $fillable = [
      'name', 'data', 'lm_country_id', 'lm_region_id'
  ];
}
