<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekCity extends Model
{
    use HasFactory;

  const NEW_TERRITORY_CITY_CODE = '101010101';
  const NEW_TERRITORY_CITY_NAME = 'Все города';
  public function cdek_region() {
    return $this->belongsTo('App\Models\CdekRegion', 'region_id');
  }

  public function pvzs() {
    return $this->hasMany('App\Models\CdekPvz', 'city_id');
  }

    protected $fillable = [
        'code',
        'city',
        'fias_guid',
        'country_code',
        'country',
        'region',
        'region_code',
        'region_id',
        'fias_region_guid',
        'sub_region',
        'longitude',
        'latitude',
        'time_zone',
        'payment_limit', 'lm_country_id', 'lm_region_id', 'lm_city_id'
    ];
}
