<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatInBagBag extends Model
{
    use HasFactory;

    protected $casts = [
        'opened_at' => 'datetime',
        'data' => 'array',
    ];

    protected $fillable = [
        'session_id',
        'position',
        'type',
        'open_index',
        'opened_at',
        'prize_id',
        'product_id',
        'prize_type',
        'nominal',
        'data',
    ];

    public function session()
    {
        return $this->belongsTo(CatInBagSession::class, 'session_id');
    }

    public function prize()
    {
        return $this->belongsTo(CatInBagPrize::class, 'prize_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
