<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomForm;
use App\Models\CustomFormData;
use App\Models\CustomFormField;
use App\Models\NpsSurvey;
use App\Models\NpsSurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomFormController extends Controller
{
    public function data()
    {
      $form = CustomForm::find(1);
      $questions = $form->questions;
      $usersData = $form->users();
      if (request()->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
        $usersData->where('custom_form_user.created_at', '>', $date_from);
      }
      if (request()->date_until) {
        $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
        $usersData->where('custom_form_user.created_at', '<', $date_until);
      }
      if(request()->email){
        $usersData->where(DB::raw('lower(email)'), 'like', '%'.trim(request()->email).'%');
      }
      if (request()->status !== null && in_array(request()->status, [0,1,2])) {
        $usersData->where('custom_form_user.status', request()->status);
      }
      if (request()->status !== null && in_array(request()->status, [0,1,2])) {
        $usersData->where('custom_form_user.status', request()->status);
      }
      if(request()->orderBy && str_contains(request()->orderBy, '|')){
        $orderBy = explode('|', request()->orderBy);
      }
      $usersData = $usersData->orderBy('custom_form_user.'.($orderBy[0] ?? 'id'), $orderBy[1] ?? 'desc')->paginate(30);
//      dd($form, $usersData->first()->formAnswers);
      $seo = [
          'title' => 'Данные форм'
      ];
      return view('template.admin.custom_form.data', compact( 'seo', 'form', 'questions', 'usersData'));
    }
  public function status(Request $request, $user_id)
  {
    $request->validate([
        'status' => 'required'
    ]);
    $custom_form_user = DB::table('custom_form_user')->where('user_id', $user_id)->first();
    if($custom_form_user && $custom_form_user->status != $request->status){
      DB::update('UPDATE `custom_form_user` SET `status`='.$request->status.' WHERE `id` = ' . $custom_form_user->id . ';');
      return response()->json(true);
    }

    return response()->json(true, 400);
  }
  public function change(Request $request, $form_id, $field_id, $user_id)
  {
    $request->validate([
        'value' => 'required'
    ]);
    $data = CustomFormData::query()
        ->where('form_id', $form_id)
        ->where('field_id', $field_id)
        ->where('user_id', $user_id)
        ->first();
    if($data && $data->value != $request->value){
      $data->update([
          'value' => $request->value
      ]);
    }

    return back();
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $forms = CustomForm::query()->paginate(20);
      $seo = [
          'title' => 'Формы ввода'
      ];
      return view('template.admin.custom_form.index', compact( 'seo', 'forms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    $seo = [
        'title' => 'Добавить кастомную форму'
    ];
    return view('template.admin.custom_form.create', compact('seo'));
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
          'name' => 'required',
      ]);
      $slug = $request->slug;
      if(!$request->slug){
        $slug = translit(mb_strtolower(str_replace(" ", "_", $request->name)));
      }else{
        $slug = translit(mb_strtolower(str_replace(" ", "_", $slug)));
      }
      $form = CustomForm::create([
          'name' => $request->name,
          'slug' => $slug,
      ]);

      if($request->carousel_data['formQuestions']){
        foreach($request->carousel_data['formQuestions'] as $order => $request_question){
          if(!isset($request_question['key'])){
            continue;
          }
          CustomFormField::create([
              'form_id' => $form->id,
              'key' => $request_question['key'],
              'description' => null,
              'is_hidden' => $request_question['is_hidden'] ?? false,
              'order' => $order
          ]);
        }
      }
      return redirect()->route('admin.custom-forms.index')->with([
          'success' => 'Форма успешно добавлена'
      ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
      $form = CustomForm::query()->where('slug', $slug)->first();
      $seo = [
          'title' => 'Изменить форму'
      ];
      return view('template.admin.custom_form.edit', compact('seo', 'form'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
      $request->validate([
          'name' => 'required',
      ]);
      $form = CustomForm::query()->where('slug', $slug)->first();
      $slug = $request->slug;
      if(!$request->slug){
        $slug = translit(mb_strtolower(str_replace(" ", "_", $request->name)));
      }else{
        $slug = translit(mb_strtolower(str_replace(" ", "_", $slug)));
      }
      $form->update([
          'name' => $request->name,
          'slug' => $slug,
      ]);

      if($request->carousel_data['formQuestions']){
        $ids = [];
        $order = 1;
        foreach($request->carousel_data['formQuestions'] as $request_question){
          if(!isset($request_question['key'])){
            continue;
          }
          if(isset($request_question['id'])){
            $question = CustomFormField::find($request_question['id']);
            $question->update([
                'form_id' => $form->id,
                'key' => $request_question['key'],
                'description' => null,
                'is_hidden' => $request_question['is_hidden'] ?? false,
                'order' => $order
            ]);
          }else{
            $question = CustomFormField::create([
                'form_id' => $form->id,
                'key' => $request_question['key'],
                'description' => null,
                'is_hidden' => $request_question['is_hidden'] ?? false,
                'order' => $order
            ]);
          }
          $ids[] = $question->id;
          $order++;
        }
        $form->fields()->whereNotIn('id', $ids)->delete();
      }
      return redirect()->route('admin.custom-forms.index')->with([
          'success' => 'Опрос успешно изменен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
