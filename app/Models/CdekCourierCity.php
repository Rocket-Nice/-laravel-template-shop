<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekCourierCity extends Model
{
    use HasFactory;

  public function cdek_region() {
    return $this->belongsTo('App\Models\CdekRegion', 'region_id');
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
      'payment_limit',
      'active', 'lm_country_id', 'lm_region_id', 'lm_city_id'
  ];
}
