<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\NpsSurvey;
use App\Models\NpsSurveyAnswer;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(NpsSurvey $survey){
      return back()->withErrors(['Этот раздел недоступен']);
      $user = auth()->user();
      if($user->surveys()->where('nps_surveys.id', $survey->id)->exists()){
        return redirect()->route('cabinet.order.index')->withErrors(['Вы уже проши опрос']);
      }
      $content = Content::find(19);
      $questions = $survey->questions()->where('is_hidden', false)->get();
      $seo = [
          'title' => 'Оцените наше качество'
      ];
      return view('template.cabinet.survey', compact('survey', 'content', 'questions', 'seo'));
    }

    public function save(Request $request, NpsSurvey $survey){
      $user = auth()->user();
      if($user->surveys()->where('nps_surveys.id', $survey->id)->exists()){
        return redirect()->route('cabinet.order.index')->withErrors(['Вы уже проши опрос']);
      }
      $questions = $survey->questions()->where('is_hidden', false)->get();
      $answers = [];
      foreach($questions as $question){
        $request_answer = $request->questions[$question->id] ?? null;
        if($request_answer === null){
          continue;
        }
        $answers[$question->id] = $request_answer;
      }
      if($questions->count() != count($answers)){
        return response()->json([
            'success' => false,
            'message' => 'Пожалуйста, ответьте на все вопросы',
            'errors' => ''
        ], 422);
      }
      foreach($answers as $question_id => $score){
        $comment = $request->comments[$question_id] ?? null;
        NpsSurveyAnswer::create([
            'user_id' => $user->id,
            'survey_question_id' => $question_id,
            'score' => $score,
            'comment' => $comment
        ]);
      }
      $user->surveys()->attach($survey->id, [
          'nps_score' => array_sum($answers) / count($answers),
          'comment' => $request->input('survey-comment'),
      ]);
      return response()->json([
          'success' => true,
          'message' => 'Ответы успешно сохранены',
          'data' => ''
      ], 200);
    }
}
