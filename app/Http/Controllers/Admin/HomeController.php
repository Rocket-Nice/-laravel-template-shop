<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(){
      if(auth()->user()->hasPermissionTo('Доступ к отчетам')){
        return redirect()->route('admin.reports.index');
      }
      return view('template.admin.dashboard');
    }
}
