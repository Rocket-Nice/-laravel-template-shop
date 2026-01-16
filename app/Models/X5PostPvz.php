<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class X5PostPvz extends Model
{
    use HasFactory;

  public $timestamps = false;
  public function region() {
    return $this->belongsTo('App\Models\X5PostRegion', 'region_id');
  }
//
  public function city() {
    return $this->belongsTo('App\Models\X5PostCity', 'city_id');
  }

  protected $casts = [
      'cellLimits' => 'array',
      'rate' => 'array',
  ];
    protected $fillable = [
        'region_id',
        'city_id',
        'pvz_id',
        'mdmCode',
        'name',
        'partnerName',
        'multiplaceDeliveryAllowed',
        'type',
        'country',
        'fullAddress',
        'shortAddress',
        'address_lat',
        'address_lng',
        'additional',
        'cellLimits',
        'returnAllowed',
        'timezoneOffset',
        'phone',
        'cashAllowed',
        'cardAllowed',
        'loyaltyAllowed',
        'extStatus',
        'rate',
        'createDate',
        'openDate',
        'timezone',
        'outsideX5',
        'created_at',
        'updated_at',
        'is_active',
    ];
}
