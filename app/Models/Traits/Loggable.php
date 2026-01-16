<?php


namespace App\Models\Traits;


use App\Models\ActivityLog;

trait Loggable
{
  public function log() {
    return $this->morphMany('App\Models\ActivityLog', 'loggable')->orderBy('created_at', 'DESC');
  }

  public function addLog($action, $text = null, $data = null) {
    $this->log()->create([
        'user_id' => auth()->id(),
        'action' => $action,
        'text' => $text,
        'data' => $data,
    ]);
  }
}
