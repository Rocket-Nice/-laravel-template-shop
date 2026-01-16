<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Region extends Model
{
    use HasFactory, QueryCacheable;

    public function country()
    {
      return $this->belongsTo('App\Models\Country', 'country_id');
    }
    public function cdek_region(){
      return $this->hasOne('App\Models\CdekRegion', 'lm_region_id');
    }
    public function boxberry_region(){
      return $this->hasOne('App\Models\BoxberryRegion', 'lm_region_id');
    }

  public function cdek_cities() {
    return $this->hasMany('App\Models\CdekCity', 'lm_region_id');
  }
  public function cdek_courier_cities() {
    return $this->hasMany('App\Models\CdekCourierCity', 'lm_region_id');
  }
  public function boxberry_cities() {
    return $this->hasMany('App\Models\BoxberryRegion', 'lm_region_id');
  }

    protected $fillable = [
        'name',
        'country_id'
    ];
}
