<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\AttachUsersToMailingListJob;
use App\Models\MailingList;
use App\Models\Page;
use App\Models\ShippingMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SafeObject;

class MailingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $mailing_lists = MailingList::orderByDesc('sending_date')->paginate(50);
      $seo = [
          'title' => 'Рассылки'
      ];
      return view('template.admin.mailing_lists.index', compact('seo', 'mailing_lists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Создать рассылку'
      ];
      return view('template.admin.mailing_lists.create', compact('seo'));
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
          'name' => 'string|required',
          'method' => 'required',
          'message' => 'nullable'
      ]);
      $sending_date = request()->sending_date ? Carbon::createFromFormat('d.m.Y H:i', request()->sending_date)->format('Y-m-d H:i:s') : null;

      MailingList::create([
          'name' => $request->name,
          'method' => $request->post('method'),
          'message' => $request->message,
        'sending_date' => $sending_date
      ]);
      return redirect()->route('admin.mailing_lists.index')->with([
          'success' => 'Новая рассылка создана'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(MailingList $mailingList)
    {
      $seo = [
          'title' => 'Изменить рассылку'
      ];
      return view('template.admin.mailing_lists.edit', compact('seo', 'mailingList'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailingList $mailingList)
    {
      $request->validate([
          'name' => 'string|required',
          'method' => 'required',
          'message' => 'nullable'
      ]);
      $sending_date = request()->sending_date ? Carbon::createFromFormat('d.m.Y H:i', request()->sending_date)->format('Y-m-d H:i:s') : null;

      $mailingList->update([
          'name' => $request->name,
          'method' => $request->post('method'),
          'message' => $request->message,
          'sending_date' => $sending_date
      ]);
      return redirect()->route('admin.mailing_lists.index')->with([
          'success' => 'Рассылка изменена'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MailingList $mailingList)
    {
      $mailingList->delete();
      return redirect()->route('admin.mailing_lists.index')->with([
          'success' => 'Рассылка удалена'
      ]);
    }

    public function add_users(Request $request)
    {
      $mailingListId = $request->mailing_list_id;
      User::select('id')->filter(new SafeObject(request()->toArray()))->chunk(5000, function ($users) use ($mailingListId) {
        $userIds = $users->pluck('id')->toArray();
        AttachUsersToMailingListJob::dispatch($mailingListId, $userIds);
      });
      return back()->with([
          'success' => 'Пользователи поставлены в очередь на добавление в рассылку'
      ]);
    }
}
