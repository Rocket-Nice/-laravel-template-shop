<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function page(Page $page){
      $seo = [
          'title' => $page->title
      ];
      return view('template.public.page', compact('seo', 'page'));
    }
}
