<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\CustomForm;
use App\Models\CustomFormData;
use App\Models\NpsSurvey;
use App\Models\NpsSurveyAnswer;
use Illuminate\Http\Request;

class CustomFormController extends Controller
{
  private function checkAccess(CustomForm $form){
    $user = auth()->user();
//    return !$user->forms()->where('custom_forms.id', $form->id)->exists() &&
//        ($user->orders()->where('created_at', '>', '2024-12-25 10:00')->where('confirm', 1)->exists() || $user->hasPermissionTo('Доступ к админпанели'));
    return false;
  }

  public function index(CustomForm $form){
    $user = auth()->user();
    if(!$this->checkAccess($form)){
      return redirect()->route('cabinet.order.index')->withErrors(['Вам недоступен этот раздел']);
    }
    $content = Content::find(19);
    $fields = $form->fields()->where('is_hidden', false)->get();
    $seo = [
        'title' => 'Загадай желание'
    ];
    return view('template.cabinet.form', compact('form', 'content', 'fields', 'seo'));
  }

  public function save(Request $request, CustomForm $form){
    $user = auth()->user();

    if(!$this->checkAccess($form)){
      return redirect()->route('cabinet.order.index')->withErrors(['Вам недоступен этот раздел']);
    }
    $fields = $form->fields()->where('is_hidden', false)->get();
    $answers = [];
    foreach($fields as $field){
      $request_answer = $request->form[$field->id] ?? null;
      if($request_answer === null){
        continue;
      }
      $answers[$field->id] = $request_answer;
    }

    if($fields->count() != count($answers)){
      return response()->json([
          'success' => false,
          'message' => 'Пожалуйста, ответьте на все вопросы',
          'errors' => ''
      ], 422);
    }
    foreach($answers as $field_id => $answer){
      CustomFormData::create([
          'form_id' => $form->id,
          'user_id' => $user->id,
          'field_id' => $field_id,
          'value' => $answer,
      ]);
    }
    $user->forms()->attach($form->id);
    return response()->json([
        'success' => true,
        'message' => 'Ваше желание принято',
        'data' => ''
    ], 200);
  }
}
