<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxberryCity extends Model
{
    use HasFactory;

  public function region() {
    return $this->belongsTo('App\Models\BoxberryRegion', 'region_id');
  }

  protected $casts = [
      'data' => 'array'
  ];

  protected $fillable = [
      'Name',
      'Code',
      'ReceptionLaP',
      'DeliveryLaP',
      'Reception',
      'ForeignReceptionReturns',
      'Terminal',
      'CourierReception',
      'Kladr',
      'Region',
      'UniqName',
      'District',
      'data', 'region_id', 'lm_country_id', 'lm_region_id', 'lm_city_id'
  ];
}
