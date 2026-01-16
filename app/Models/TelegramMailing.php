<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramMailing extends Model
{
    use HasFactory;

  public function mailing() {
    return $this->belongsTo('App\Models\MailingList', 'mailing_id', 'id');
  }

    protected $casts = [
        'filter' => 'array',
        'data' => 'array',
        'send_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'message',
        'image',
        'video',
        'send_at',
        'filter',
        'status',
        'data',
        'mailing_id',
    ];
}
