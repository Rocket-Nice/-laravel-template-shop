<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatInBagSession extends Model
{
    use HasFactory;

    protected $casts = [
        'visible_category_ids' => 'array',
        'data' => 'array',
    ];

    protected $fillable = [
        'order_id',
        'user_id',
        'total',
        'visible_category_ids',
        'bag_count',
        'open_limit',
        'opened_count',
        'status',
        'data',
    ];

    public function bags()
    {
        return $this->hasMany(CatInBagBag::class, 'session_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
