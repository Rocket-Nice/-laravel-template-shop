<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NpsSurvey;
use App\Models\NpsSurveyAnswer;
use App\Models\NpsSurveyQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
  public function statistic()
  {
    $questions = NpsSurveyQuestion::get();
    $results = [];
//    $results['Всего'] = DB::table('nps_survey_user')
//        ->selectRaw('AVG(nps_score) as average_score,
//            COUNT(*) as total_scores_count,
//            COUNT(CASE WHEN nps_score > 8 THEN 1 END) as high_scores_count,
//            COUNT(CASE WHEN nps_score BETWEEN 7 AND 8 THEN 1 END) as medium_scores_count,
//            COUNT(CASE WHEN nps_score < 7 THEN 1 END) as low_scores_count')
//        ->first();
    $total_statistic = DB::table('nps_survey_answers')
        ->selectRaw('AVG(score) as average_score,
            COUNT(*) as total_scores_count,
            COUNT(CASE WHEN score > 8 THEN 1 END) as high_scores_count,
            COUNT(CASE WHEN score BETWEEN 7 AND 8 THEN 1 END) as medium_scores_count,
            COUNT(CASE WHEN score < 7 THEN 1 END) as low_scores_count')
        ->where('survey_question_id', '!=', 15);
    if (request()->date_from) {
      $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
      $total_statistic->where('nps_survey_answers.created_at', '>', $date_from);
    }
    if (request()->date_until) {
      $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
      $total_statistic->where('nps_survey_answers.created_at', '<', $date_until);
    }
    $total_statistic = $total_statistic->first();
    $results['Всего'] = $total_statistic;
    foreach($questions as $question){
      $question_statistic = $question->answers()
          ->selectRaw('AVG(score) as average_score,
            COUNT(*) as total_scores_count,
            COUNT(CASE WHEN score > 8 THEN 1 END) as high_scores_count,
            COUNT(CASE WHEN score BETWEEN 7 AND 8 THEN 1 END) as medium_scores_count,
            COUNT(CASE WHEN score < 7 THEN 1 END) as low_scores_count');
      if (request()->date_from) {
        $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
        $question_statistic->where('nps_survey_answers.created_at', '>', $date_from);
      }
      if (request()->date_until) {
        $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
        $question_statistic->where('nps_survey_answers.created_at', '<', $date_until);
      }
      $question_statistic = $question_statistic->first();
      $results[$question->text] = $question_statistic;
    }

    $seo = [
        'title' => 'НПС Оценки'
    ];
    return view('template.admin.nps.statistic', compact( 'seo', 'results'));
  }

  public function comments()
  {

    $comments = DB::table('nps_survey_user')
        ->leftJoin('users', 'nps_survey_user.user_id', '=', 'users.id');
    if(request()->question && is_numeric(request()->question)){
      $comments->leftJoin('nps_survey_answers', 'nps_survey_user.user_id', '=', 'nps_survey_answers.user_id')
          ->select('nps_survey_user.created_at',
              'nps_survey_user.nps_score',
              'nps_survey_user.comment',
              'users.id as user_id',
              'users.name',
              'users.email',
              'users.phone',
              'nps_survey_answers.score',
          )
          ->where('nps_survey_answers.survey_question_id', request()->question);
      if (request()->score_from) {
        $comments->where('nps_survey_answers.score', '>', (int)request()->score_from);
      }
      if (request()->score_to) {
        $comments->where('nps_survey_answers.score', '<', (int)request()->score_to);
      }
    }else{
      $comments->select('nps_survey_user.created_at',
          'nps_survey_user.nps_score',
          'nps_survey_user.comment',
          'nps_survey_user.status',
          'nps_survey_user.id as survey_user_id',
          'users.id as user_id',
          'users.name',
          'users.email',
          'users.phone',
      );
      if (request()->score_from) {
        $comments->where('nps_survey_user.nps_score', '>', (int)request()->score_from);
      }
      if (request()->score_to) {
        $comments->where('nps_survey_user.nps_score', '<', (int)request()->score_to);
      }
    }
//    $comments->whereNotNull('nps_survey_user.comment');
    if (request()->date_from) {
      $date_from = date('Y-m-d H:i:s', strtotime(request()->date_from));
      $comments->where('nps_survey_user.created_at', '>', $date_from);
    }
    if (request()->date_until) {
      $date_until = date('Y-m-d H:i:s', strtotime(request()->date_until));
      $comments->where('nps_survey_user.created_at', '<', $date_until);
    }
    if (request()->status !== null && in_array(request()->status, [0,1,2])) {
      $comments->where('nps_survey_user.status', request()->status);
    }
    if(request()->orderBy && str_contains(request()->orderBy, '|')){
      $orderBy = explode('|', request()->orderBy);
    }
    if(request()->question && is_numeric(request()->question) && ($orderBy[0] ?? false) && $orderBy[0] == 'nps_score'){
      $orderBy[0] = 'score';
    }
    $prefix = request()->question && is_numeric(request()->question) ? 'nps_survey_answers.' : 'nps_survey_user.';
    $comments = $comments->orderBy($prefix.($orderBy[0] ?? 'id'), $orderBy[1] ?? 'desc')->paginate(50);
    $questions = NpsSurveyQuestion::get();
    $seo = [
        'title' => 'НПС Комментарии'
    ];
    return view('template.admin.nps.comments', compact( 'seo', 'comments', 'questions'));
  }

  public function survey(User $user)
  {
    $answers = NpsSurveyAnswer::query()
        ->with('question')
        ->select('score', 'comment', 'survey_question_id')
        ->where('user_id', $user->id)
        ->get();
    return response()->json($answers);
  }
  public function status(Request $request, $survey_user_id)
  {
    $request->validate([
        'status' => 'required'
    ]);
    $survey_user = DB::table('nps_survey_user')->where('id', $survey_user_id)->first();
    if($survey_user && $survey_user->status != $request->status){
      DB::update('UPDATE `nps_survey_user` SET `status`='.$request->status.' WHERE `id` = ' . $survey_user_id . ';');
      return response()->json(true);
    }

    return response()->json(true, 400);
  }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $surveys = NpsSurvey::query()->paginate(20);
        $seo = [
            'title' => 'Опросы НПС'
        ];
        return view('template.admin.nps.index', compact( 'seo', 'surveys'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      $seo = [
          'title' => 'Создать НПС опрос'
      ];
      return view('template.admin.nps.create', compact('seo'));
    }

    /**
     * Store a newly created resource in storage.
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
        $survey = NpsSurvey::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);
        if($request->carousel_data['surveyQuestions']){
          foreach($request->carousel_data['surveyQuestions'] as $order => $request_question){
            if(!isset($request_question['text'])){
              continue;
            }
            NpsSurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $request_question['text'],
                'comment_text' => $request_question['comment_text'] ?? null,
                'description' => null,
                'is_hidden' => $request_question['is_hidden'] ?? false,
                'order' => $order
            ]);
          }
        }
        return redirect()->route('admin.nps.index')->with([
            'success' => 'Опрос успешно добавлен'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($slug)
    {
      $survey = NpsSurvey::query()->where('slug', $slug)->first();
      $seo = [
          'title' => 'Изменить опрос'
      ];
      return view('template.admin.nps.edit', compact('seo', 'survey'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
      $request->validate([
          'name' => 'required',
      ]);
      $survey = NpsSurvey::query()->where('slug', $slug)->first();
      $slug = $request->slug;
      if(!$request->slug){
        $slug = translit(mb_strtolower(str_replace(" ", "_", $request->name)));
      }else{
        $slug = translit(mb_strtolower(str_replace(" ", "_", $slug)));
      }
      $survey->update([
          'name' => $request->name,
          'slug' => $slug,
      ]);
      if($request->carousel_data['surveyQuestions']){
        $ids = [];
        $order = 1;
        foreach($request->carousel_data['surveyQuestions'] as $request_question){
          if(!isset($request_question['text'])){
            continue;
          }
          if(isset($request_question['id'])){
            $question = NpsSurveyQuestion::find($request_question['id']);
            $question->update([
                'text' => $request_question['text'],
                'comment_text' => $request_question['comment_text'] ?? null,
                'description' => null,
                'is_hidden' => $request_question['is_hidden'] ?? false,
                'order' => $order
            ]);
          }else{
            $question = NpsSurveyQuestion::create([
                'survey_id' => $survey->id,
                'text' => $request_question['text'],
                'comment_text' => $request_question['comment_text'] ?? null,
                'description' => null,
                'is_hidden' => $request_question['is_hidden'] ?? false,
                'order' => $order
            ]);
          }
          $ids[] = $question->id;
          $order++;
        }
        $survey->questions()->whereNotIn('id', $ids)->delete();
      }
      return redirect()->route('admin.nps.index')->with([
          'success' => 'Опрос успешно изменен'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
      $survey = NpsSurvey::query()->where('slug', $slug)->first();
      $survey->delete();
      return redirect()->route('admin.nps.index')->with([
          'success' => 'Опрос удалена'
      ]);
    }
}
