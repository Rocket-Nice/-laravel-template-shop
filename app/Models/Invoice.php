<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

  public function orders() {
    return $this->belongsToMany('App\Models\Order', 'order_invoice');
  }

    protected $casts = [
        'query' => 'array',
        'options' => 'array'
    ];
    protected $fillable = [
        'name', 'query', 'options'
    ];
}
