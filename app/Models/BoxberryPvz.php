<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoxberryPvz extends Model
{
    use HasFactory;

  public $timestamps = false;
  public function region() {
    return $this->belongsTo('App\Models\BoxberryRegion', 'region_id');
  }

  public function city() {
    return $this->belongsTo('App\Models\BoxberryCity', 'city_id');
  }

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'work_schedule',
        'trip_description',
        'delivery_period',
        'city_code',
        'city_name',
        'tariff_zone',
        'settlement',
        'area',
        'country',
        'only_prepaid_orders',
        'address_reduce',
        'acquiring',
        'digital_signature',
        'type_of_office',
        'nalKD',
        'metro',
        'volume_limit',
        'load_limit',
        'GPS',
        'region_id',
        'city_id',
        'created_at',
        'updated_at',
    ];
}
