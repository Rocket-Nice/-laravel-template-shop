<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatInBagPreview extends Model
{
    use HasFactory;

    protected $casts = [
        'category_ids' => 'array',
        'expires_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'guest_key',
        'category_ids',
        'refresh_count',
        'expires_at',
    ];
}
