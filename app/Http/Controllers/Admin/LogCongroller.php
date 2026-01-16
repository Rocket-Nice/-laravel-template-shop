<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class LogCongroller extends Controller
{
    public function index()
    {
      $log = ActivityLog::query();
      if(request()->type&&request()->type!='{all}'){
        $log->where('loggable_type', request()->type);
      }
      if(request()->id){
        $log->where('loggable_id', request()->id);
      }
      $log = $log->orderBy('created_at', 'DESC')->paginate(100);

      $seo = [
          'title' => 'История действий'
      ];
      return view('template.admin.log.index', compact('log', 'seo'));
    }

    public function show($id){
      $log_item = ActivityLog::findOrFail($id);
      return view('template.admin.log.show', compact('log_item'));
    }
}
