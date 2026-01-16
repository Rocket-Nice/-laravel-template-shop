<?php

namespace App\Http\Controllers\Admin\Goods;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
  public function index(Request $request)
  {
    $comments = Comment::select()
        ->join('users', 'comments.user_id', '=', 'users.id')
        ->select(
            'comments.*',
            'users.id as user_id',
            'users.first_name',
            'users.middle_name',
            'users.last_name',
            'comments.created_at as created_at'
        );
    if(request()->name){
      $comments->where(DB::raw('lower(users.name)'), 'like', '%'.trim(request()->name).'%');
    }
    if(request()->email){
      $comments->where(DB::raw('lower(users.email)'), 'like', '%'.trim(request()->email).'%');
    }
    if(request()->phone){
      $comments->where(DB::raw('lower(users.phone)'), 'like', '%'.trim(request()->phone).'%');
    }
    if(request()->get('reviewStatus') == 'y'){
      $comments->where('hidden', 1);
    }elseif(request()->get('reviewStatus') == 'n'){
      $comments->where('hidden', 0);
    }
    if($request->date_from){
      $date_from = Carbon::parse($request->date_from);
      $comments->where('comments.created_at', '>=', $date_from->format('Y-m-d H:i:s'));
    }
    if($request->date_until){
      $date_to = Carbon::parse($request->date_until);
      $comments->where('comments.created_at', '<=', $date_to->format('Y-m-d H:i:s'));
    }
    if ($request->product){
      $products = Product::whereIn('sku', $request->product)->pluck('id')->toArray();
      $comments->where('commentable_type', 'App\Models\Product')->whereIn('commentable_id', $products);
    }
    if ($request->user){
      $comments->where('users.id', $request->user);
    }
//    if(auth()->id()==1){
//      dd($comments->toSql(), $date_from->format('Y-m-d H:i:s'), $products);
//    }
    $comments = $comments->orderBy('comments.id', 'desc')->paginate(100);

    $seo = [
        'title' => 'Отзывы'
    ];

    $products = Product::select('id', 'name', 'sku')->where('type_id', 1)->get();
    return view('template.admin.reviews.index', compact('comments', 'seo', 'products'));
  }
  public function update(Request $request)
  {
    $request->validate([
        'comments_ids' => ['required', 'array'],
        'action' => ['required', 'string'],
    ]);
    $comment_ids = $request->comments_ids;
    $action = explode('|', $request->action);
    $comments = Comment::select()->whereIn('id', $comment_ids);

    $message_success = '';
    if ($action[0] == 'set_status'){ // устанавливаем статус
      $comments = $comments->get();
      $status = $action[1];
      foreach($comments as $comment){
        $comment->update([
            'hidden' => $status
        ]);
        if(!$status&&(!isset($comment->data['bonused'])||!$comment->data['bonused'])){
          $comment->user->addBonuses(250, 'Товар '.$comment->commentable->sku);
          $comment->update([
              'data' => [
                  'bonused' => true
              ]
          ]);
        }elseif($status&&isset($comment->data['bonused'])&&$comment->data['bonused']){
          $comment->user->subBonuses(250, 'Товар '.$comment->commentable->sku);
          $comment->update([
              'data' => [
                  'bonused' => false
              ]
          ]);
        }
      }
      $message_success = 'Статус успешно изменен';
    }elseif ($action[0] == 'remove'){ // устанавливаем статус
      $comments = $comments->get();
      $status = $action[1];
      foreach($comments as $comment){
        $comment->delete();
      }
      $message_success = 'Комментарии успешно удалены';
    }
    // Comment::flushQueryCache();
    return back()->with(['status' => $message_success]);
  }
  public function answer(Request $request)
  {
    $request->validate([
        'review_id' => ['required', 'exists:comments,id'],
        'text' => ['required', 'string'],
    ]);
    $comments = Comment::find($request->review_id);
    $reply = [
        'date' => date('Y-m-d H:i'),
        'name' => 'Администратор',
        'text' => $request->text
    ];
    $comments->update([
        'data' => $reply
    ]);
    // Comment::flushQueryCache();
    return back()->with(['status' => 'Ответ успешно добавлен']);
  }

  public function image(Comment $comment, $index)
  {
    if(isset($comment->files[$index])){
      $files = $comment->files;
      $files[$index]['hidden'] = ($comment->files[$index]['hidden'] ?? false) ? false : true;
      $comment->update(
          [
              'files' => $files
          ]
      );
      return response()->json(['success' => true], 200);
    }
    return response()->json(['success' => false], 200);
  }
}
