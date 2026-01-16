<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'path', 'file_id', 'thumbnail_id'
    ];
}
