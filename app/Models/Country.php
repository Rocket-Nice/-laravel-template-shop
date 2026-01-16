<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Country extends Model
{
    use HasFactory;
    use QueryCacheable;

    public $cacheFor = 3600*24;
    //public $cacheFor = 3600*24;

    public const DELIVERY = [
        'cdek' => 'СДЭК до ПВЗ',
        'cdek_courier' => 'СДЭК Курьер',
        'boxberry' => 'Boxberry Доставка',
        'bxb' => 'Международная доставка',
        'pochta' => 'Доставка Почтой'
    ];

    public function scopeOrderAvailable(Builder $builder)
    {
      $builder->whereIn('id', [1,14,46,57,2])
          ->where('options->status', '!=', null)
          ->orderBy('name', 'asc');
    }
    protected $casts = [
        'options' => 'array'
    ];

    protected $fillable = [
        'name',
        'options'
    ];
}
