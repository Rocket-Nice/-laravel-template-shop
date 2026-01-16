<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekRegion extends Model
{
    use HasFactory;

  const NEW_TERRITORY_REGION_CODE = '101010101';
  const NEW_TERRITORY_REGION_NAME = 'Новые регионы';
  public function cities() {
    return $this->hasMany('App\Models\CdekCity', 'region_id');
  }

  public function lm_region(){
    return $this->belongsTo('App\Models\Region', 'lm_region_id');
  }

  public function country(){
    return $this->belongsTo('App\Models\Country', 'lm_country_id');
  }
    public function pvzs() {
      return $this->hasMany('App\Models\CdekPvz', 'region_id');
    }

    protected $fillable = [
        'country_code',
        'country',
        'region',
        'region_code',
        'fias_region_guid',
        'lm_country_id', 'lm_region_id'
    ];
}
