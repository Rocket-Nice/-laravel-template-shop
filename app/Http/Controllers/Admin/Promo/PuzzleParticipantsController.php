<?php

namespace App\Http\Controllers\Admin\Promo;

use App\Http\Controllers\Controller;
use App\Models\PuzzleImage;
use App\Services\PuzzleService\Client;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PuzzleParticipantsController extends Controller
{
  private $client;

  public function __construct(){
    $this->client = new Client();
  }
  public function index()
  {
    $this->client->getToken();
    $puzzleImages = PuzzleImage::query()->with('user');
    if(request()->only_correct){
      $puzzleImages->where('is_correct', true);
    }
    if(request()->keyword){
      $keyword = request()->keyword;
      $puzzleImages->whereHas('user', function(Builder $builder) use ($keyword){
        $keyword = mb_strtolower($keyword);
        $builder->where(DB::raw('name'), 'like', '%' . $keyword . '%')
            ->orWhere(DB::raw('phone'), 'like', '%' . $keyword . '%')
            ->orWhere(DB::raw('email'), 'like', '%' . $keyword . '%');
      });
    }
    $puzzleImages = $puzzleImages->orderByDesc('created_at')->paginate(100);
    $member_ids = $puzzleImages->pluck('member_id')->toArray();
    $prizes = $this->client->prizeByParticipants(['member_ids' => $member_ids]);
    if(is_array($prizes)){
      foreach($puzzleImages as $key => $puzzleImage){
        if($puzzleImage->member_id){
          $puzzleImages[$key]->prize = searchInMultidimensionalArray($prizes, 'member_id', $puzzleImage->member_id);
        }
      }
    }

    $seo = [
        'title' => 'Призы для акции "Собери пазл"'
    ];
    return view('template.admin.promo.puzzles.participants.index', compact('seo', 'puzzleImages'));
  }

  public function update(Request $request, PuzzleImage $puzzleImage){
    $this->client->getToken();
    $member_id = $puzzleImage->member_id;
    if($member_id){
      if($request->is_correct){ // дать подарок
        $this->client->assignParticipant($member_id);
      }else{
        $this->client->resetPrizeParticipant($member_id);
      }
    }
    $puzzleImage->update([
        'is_correct' => $request->is_correct,
    ]);

    return redirect()->route('admin.puzzle_participants.index')->with([
        'success' => 'Данные успешно изменены'
    ]);
  }
}
