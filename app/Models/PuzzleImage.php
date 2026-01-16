<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PuzzleImage extends Model
{
    use HasFactory;


  public function user()
  {
    return $this->belongsTo('App\Models\User');
  }

    protected $casts = [
        'result' => 'array'
    ];
    protected $fillable = [
        'user_id',
        'image_path',
        'thumb_path',
        'result_message',
        'result',
        'member_id',
        'has_prize',
        'is_correct',
    ];
}
