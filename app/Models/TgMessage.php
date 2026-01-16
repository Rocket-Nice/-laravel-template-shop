<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TgMessage extends Model
{
    use HasFactory;

  public function user() {
    return $this->belongsTo('App\Models\User');
  }

  public function tgChat() {
    return $this->belongsTo('App\Models\TgChat');
  }
  
    protected $casts = [
        'time' => 'datetime:Y-m-d H:i:s',
        'data' => 'array'
    ];

    protected $fillable = [
        'tg_chat_id',
        'user_id',
        'tg_message_id',
        'text',
        'time',
        'data',
        'outgoing_message',
        'delivered',
    ];
}
