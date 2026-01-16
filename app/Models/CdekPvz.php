<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdekPvz extends Model
{
    use HasFactory;

    public function cdek_region() {
      return $this->belongsTo('App\Models\CdekRegion', 'region_id');
    }
//
    public function cdek_city() {
      return $this->belongsTo('App\Models\CdekCity', 'city_id');
    }

    protected $fillable = [
        'code',
        'type',
        'country_code',
        'region_code',
        'region',
        'region_id',
        'city_code',
        'city',
        'city_id',
        'fias_guid',
        'postal_code',
        'longitude',
        'latitude',
        'address',
        'address_full',
        'pvz_data',
        'is_active',
        'is_updating',
    ];
}
