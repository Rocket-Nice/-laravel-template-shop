<?php

namespace App\Http\Controllers\Admin\Promo;

use App\Http\Controllers\Controller;
use App\Models\PuzzleImage;
use App\Models\User;
use App\Services\PuzzleService\Client;
use Illuminate\Http\Request;

class PuzzlePrizeController extends Controller
{
    private $client;

    public function __construct(){
      $this->client = new Client();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $this->client->getToken();
      $prizes = $this->client->getPrizes(['limit' => 200]);
      if(is_array($prizes)){
        usort($prizes, function($a, $b) {
          return $a['order'] <=> $b['order'];
        });
      }
      $member_ids = [];
      foreach($prizes as $prize){
        if($prize['member_id'] ?? false){
          $member_ids[] = $prize['member_id'];
        }
      }
      $puzzleImages = PuzzleImage::query()->with('user')->whereIn('member_id', $member_ids)->where('is_correct', true)->get();
      foreach($prizes as $key => $prize){
        if($prize['member_id'] ?? false){
          $prizeImage = $puzzleImages->where('member_id', $prize['member_id'])->first();
          if(!$prizeImage){
            $prizes[$key]['user'] = 'Победитель на дкд';
            continue;
          }
          $prizes[$key]['user'] = $puzzleImages->where('member_id', $prize['member_id'])->first()->user->email;
          $prizes[$key]['image_path'] = $puzzleImages->where('member_id', $prize['member_id'])->first()->image_path;
          $prizes[$key]['thumb_path'] = $puzzleImages->where('member_id', $prize['member_id'])->first()->thumb_path;
        }
      }
      $seo = [
          'title' => 'Призы для акции "Собери пазл"'
      ];
      return view('template.admin.promo.puzzles.index', compact('seo', 'prizes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $seo = [
          'title' => 'Добавить подарок "Собери пазл"'
      ];
      return view('template.admin.promo.puzzles.create', compact('seo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
      $this->client->getToken();
      $params = $request->toArray();
      unset($params['_token']);

      $prize = $this->client->createPrize($params);
      if(!is_array($prize)){
        return back()->withErrors([
            $prize->getMessage()
        ]);
      }else{
        return redirect()->route('admin.puzzles.index')->with([
            'success' => 'Новый подарок добавлен. ID '.$prize['id'] ?? ''
        ]);
      }
      return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit($id)
    {
      $this->client->getToken();
      $prize = $this->client->getPrize($id);
      if($prize['member_id']){
        $puzzleImage = PuzzleImage::query()->where('member_id', $prize['member_id'])->first();
        if($puzzleImage){
          $prize['user'] = $puzzleImage->user->email;
        }else{
          $prize['user'] = 'Победитель на сайте дкд';
        }
      }
      $seo = [
          'title' => 'Редактировать подарок "Собери пазл"'
      ];
      return view('template.admin.promo.puzzles.edit', compact('seo', 'prize'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
      $this->client->getToken();
      $request->validate([
          'email' => 'nullable|exists:users,email'
      ]);
      $params = $request->toArray();
      unset($params['_token']);
      if($request->email){
        $user = User::query()->where('email', mb_strtolower($request->email))->first();
        if(!$user){
          return back()->withErrors([
                'Пользователь не найден'
          ]);
        }
        $member = $this->client->createParticipant([
            'fio' => trim($user->last_name.' '.$user->first_name),
            'email' => $user->email,
            'id_lm' => $user->id
        ]);
        if(is_array($member)){
          $puzzleImage = PuzzleImage::create([
              'user_id' => $user->id,
              'image_path' => 0,
              'thumb_path' => 0,
              'has_prize' => true,
              'is_correct' => true,
              'member_id' => $member['id']
          ]);
          $params['member_id'] = $member['id'];
        }else{
          dd($member);
        }

      }
      $prize = $this->client->updatePrize($id, $params);
      if(!is_array($prize)){
        return back()->withErrors([
            $prize->getMessage()
        ]);
      }else{
        return redirect()->route('admin.puzzles.index')->with([
            'success' => 'Подарок успешно изменен. ID '.$prize['id'] ?? ''
        ]);
      }
      return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
      $this->client->getToken();
      $prize = $this->client->deletePrize($id);
      if(!is_array($prize)){
        return back()->withErrors([
            $prize->getMessage()
        ]);
      }else{
        return redirect()->route('admin.puzzles.index')->with([
            'success' => 'Подарок успешно удален'
        ]);
      }
      return back();
    }
}
