<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class X5PostRegion extends Model
{
    use HasFactory;

  public function cities() {
    return $this->hasMany('App\Models\X5PostCity', 'region_id');
  }
  public function pvzs() {
    return $this->hasMany('App\Models\X5PostPvz', 'region_id');
  }

    protected $fillable = [
        'name',
        'region_type',
        'lm_country_id',
        'lm_region_id',
    ];
}
