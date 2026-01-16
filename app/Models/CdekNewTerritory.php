<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CdekNewTerritory extends Model
{

    public function pvz(){
      return $this->belongsTo('App\Models\CdekPvz', 'pvz_id', 'id');
    }
    protected $fillable = [
        'address',
        'code',
        'phone',
        'email',
        'pvz_id'
    ];
}
