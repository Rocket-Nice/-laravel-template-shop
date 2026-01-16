<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SearchQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchQueryController extends Controller
{
    public function index()
    {
      $searchQueries = SearchQuery::query();
      if (request()->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
        $searchQueries->where('created_at', '>', $date_from);
      }
      if (request()->date_until) {
        $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
        $searchQueries->where('created_at', '<', $date_until);
      }
      if (request()->get('query')) {
        $searchQueries->where(DB::raw('lower(query)'), 'like', '%'.mb_strtolower(trim(request()->get('query'))).'%');
      }
      $searchQueries = $searchQueries->orderBy('created_at', 'desc')->paginate(200);
      $seo = [
          'title' => 'Поисковые запросы'
      ];
      return view('template.admin.search_queries.index', compact('seo', 'searchQueries'));
    }
}
