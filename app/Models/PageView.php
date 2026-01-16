<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use HasFactory;

    public function partner(){
      return $this->belongsTo(Partner::class);
    }

    protected $fillable = [
        'partner_id',
        'date',
        'ip',
        'referer',
        'userAgent'
    ];
}
