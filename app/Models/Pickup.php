<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Pickup extends Model
{
    use HasFactory;
    use QueryCacheable;

  public function scopeOrder(Builder $builder){
    $builder
        ->where('status', true)
        ->where('params->status', '!=', null)
        ->where('params->quantity', '!=', null);
  }

    public const CARDS = [
        'card_01' => [
            'name' => 'Текст по центру и подпись',
            'fields' => [

            ]
        ]
    ];

    public $cacheFor = 3600*24;

    public function getPickups()
    {
      return $this->where('params->status', '!=', null)->where('params->quantity', '!=', null);
    }

    protected $casts = [
        'params' => 'array'
    ];

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'status',
        'params'
    ];
}
