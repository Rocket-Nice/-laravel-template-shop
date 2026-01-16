<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

  public function orders() {
    return $this->belongsToMany('App\Models\Order', 'order_ticket');
  }
  public function parent()
  {
    return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
  }

  public function tickets()
  {
    return $this->hasMany(Ticket::class);
  }

  public function getOrders()
  {
    $orders_count = 0;
    if($this->tickets()->count()){
      foreach($this->tickets as $ticket){
        $orders_count += $ticket->orders->count();
      }
    }
    return $orders_count;
  }


  protected $fillable = [
      'file_name',
      'file_path',
      'items_count',
      'delivery_code',
      'data',
      'ticket_id'
  ];
}
