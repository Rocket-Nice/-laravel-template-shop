<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Comment extends Model
{
    use HasFactory;

  protected $casts = [
      'data' => 'array',
      'files' => 'array'
  ];

    protected $fillable = [
        'user_id',
        'rating',
        'rQuality',
        'rAroma',
        'rStructure',
        'rEffect',
        'rShipping',
        'hidden',
        'text',
        'data',
        'files',
    ];


    public function commentable()
    {
      return $this->morphTo();
    }

    public function user()
    {
      return $this->belongsTo('App\Models\User');
    }
}
