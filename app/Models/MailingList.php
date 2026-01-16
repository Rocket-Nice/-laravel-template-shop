<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    use HasFactory;

    public function users(){
      return $this->belongsToMany(User::class, 'mailing_lists_users', 'mailing_list_id', 'user_id');
    }

    protected $casts = [
        'sending_date' => 'datetime:Y-m-d H:i:s',
    ];
    protected $fillable = [
        'name', 'sending_date', 'method', 'message'
    ];
}
