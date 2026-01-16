<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashaMailList extends Model
{
    use HasFactory;


  public function users(){
    return $this->belongsToMany(User::class, 'dasha_mail_lists_users', 'user_id', 'dasha_mail_list_id');
  }

    protected $fillable = [
        'list_id',
        'name',
        'count_subscribers',
        'count_active_subscribers',
    ];
}
