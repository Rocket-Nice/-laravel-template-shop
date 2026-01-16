<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\PriceCategory;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $pages = Page::paginate(50);
      $seo = [
          'title' => 'Страницы'
      ];
      return view('template.admin.pages.index', compact('seo', 'pages'));
    }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $seo = [
        'title' => 'Добавить страницу'
    ];

    return view('template.admin.pages.create', compact('seo'));
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'string|required',
            'content' => 'string|required',
            'main_page' => 'boolean'
        ]);
        $slug = translit($request->title);
        $exists_page = Page::where('slug', $slug)->count();
        if($exists_page){
          $slug .= getCode(3);
        }
        $page = Page::create([
            'title' => $request->title,
            'content' => $request->input('content'),
            'main_page' => $request->main_page ?? false,
            'slug' => $slug
        ]);
        Page::flushQueryCache();
        $page->addLog('Создана страница «'.$page->title.'»');
        return redirect()->route('admin.pages.index')->with([
            'success' => 'Страница успешно добавлена'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Page $page)
    {
      $seo = [
          'title' => 'Редактировать страницу «'.$page->title.'»'
      ];

      return view('template.admin.pages.edit', compact('seo', 'page'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Page $page)
    {
      $request->validate([
          'title' => 'string|required',
          'content' => 'string|required',
          'main_page' => 'boolean'
      ]);
      $page_params = [
          'title' => $request->title,
          'content' => $request->input('content'),
          'main_page' => $request->main_page ?? false
      ];
      $old = $page->toArray();
      $page->update($page_params);
      Page::flushQueryCache();
      $page->addLog('Изменена страница «'.$page->title.'»', null, [
          'old' => $old,
          'new' => $page_params
      ]);
      return redirect()->route('admin.pages.index')->with([
          'success' => 'Страница успешно изменена'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
      $page->delete();
      Page::flushQueryCache();
      return redirect()->route('admin.pages.index')->with([
          'success' => 'Страница удалена'
      ]);
    }
}
