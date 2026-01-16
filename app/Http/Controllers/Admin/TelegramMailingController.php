<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailingList;
use App\Models\TelegramMailing;
use App\Models\TgChat;
use App\Models\TgFile;
use App\Models\User;
use App\Jobs\SendTelegramMailingJob;
use App\Notifications\TelegramNotification;
use App\Services\Telegram\Client;
use App\Services\Telegram\Entities\Video;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SafeObject;

class TelegramMailingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
  {
//      $mailing_lists = MailingList::orderByDesc('sending_date')->paginate(50);
    $test_users = User::query()->whereIn('id', [1,333134,314738])->get();
    $mailing_lists = TelegramMailing::orderByDesc('send_at')->paginate(50);
    $jobs = DB::table('jobs')
        ->where('queue', 'like', 'tg_queue_%')
        ->select([
            DB::raw('COUNT(*) as total_count'),
            DB::raw('MIN(available_at) as min_available_at'),
            DB::raw('MAX(CASE WHEN reserved_at IS NOT NULL THEN 1 ELSE 0 END) as has_reserved')
        ])
        ->groupBy('available_at')
        ->get();
    $seo = [
        'title' => 'Рассылки в telegram'
    ];
    return view('template.admin.telegram_mailing.index', compact('seo', 'mailing_lists', 'jobs', 'test_users'));
  }

  public function prepareForSending(Request $request, $id)
  {
    $tgMailing = TelegramMailing::findOrFail($id);
    if($request->type==1 && !$request->users){
      return redirect()->route('admin.telegram_mailing.index')->withErrors([
          'Не выбраны пользователи для тестовой отправки'
      ]);
    }elseif(!in_array($request->type, [1,2])){
      return redirect()->route('admin.telegram_mailing.index')->withErrors([
          'Режим отправки не выбран'
      ]);
    }//
    SendTelegramMailingJob::dispatch($id, $request->type, $request->users)->onQueue('telegram_mailing');
    return redirect()->route('admin.telegram_mailing.index')->with([
        'success' => 'Рассылка готовится к запуску'
    ]);
  }

  public function send($id, $type, $users)
  {
    $tgMailing = TelegramMailing::findOrFail($id);
    if($type==1){
      $users = User::query()->whereIn('id', $users)->pluck('id')->toArray();
    }elseif($type==2){
      $users = User::query()
          ->select('id')
          ->filter(new SafeObject($tgMailing->filter))
          ->whereHas('tgChats', function(Builder $builder){
            $builder->where('active', true);
          })
          ->where('is_subscribed_to_marketing', true)
          ->pluck('id')->toArray();
    }else{
      return redirect()->route('admin.telegram_mailing.index')->withErrors([
          'Режим отправки не выбран'
      ]);
    }
    $image = $tgMailing->image;
    $video = $tgMailing->video;
    $message = $tgMailing->message;
    $send_at = $tgMailing->send_at->format('Y-m-d H:i');
    $mailing = $tgMailing->mailing;
    TgChat::query()->with('user')
        ->whereIn('user_id', $users)
        ->where('active', true)
        ->chunk(500, function ($tgChats) use ($message, $image, $video, $send_at, $mailing) {
          foreach($tgChats as $tgChat){
            $queue_id = mt_rand(1,8);
            $tgChat->user->mailing_list()->syncWithoutDetaching($mailing);
            if($video){
              $notification = new TelegramNotification($message, 'video_text_message', 'HTML', $video, 'tg_queue_'.$queue_id);
            }elseif(!$image){
              $notification = new TelegramNotification($message, 'text_message', 'HTML', null, 'tg_queue_'.$queue_id);
            }else{
              $notification = new TelegramNotification($message, 'image_text_message', 'HTML', $image, 'tg_queue_'.$queue_id);
            }
            $notification->delay(\Illuminate\Support\Carbon::parse($send_at));
            $tgChat->notify($notification);
          }
        });
    return redirect()->route('admin.telegram_mailing.index')->with([
        'success' => 'Рассылка успешно запущена'
    ]);
  }
    /**
     * Show the form for creating a new resource.
     */
  public function create(Request $request)
  {
    $users = User::query()
        ->select('id')
        ->filter(new SafeObject($request->toArray()))
        ->whereHas('tgChats', function(Builder $builder){
          $builder->where('active', true);
        })
        ->where('is_subscribed_to_marketing', true)
        ->count();
    $working_dir = '/shares/tg_mailing';
    if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
      mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
    }
    $seo = [
        'title' => 'Новая рассылка в telegram'
    ];
    return view('template.admin.telegram_mailing.create', compact('seo', 'users', 'working_dir'));
  }

  private function sendTestVideo($video){
    $tgFile = TgFile::query()->where('path', $video)->first();
    if($tgFile){
      return [$tgFile->file_id, $tgFile->thumbnail_id];
    }
    $tgClient = new Client();
    $tgChat = TgChat::find(1);
    $message = new Video();
    $message->setVideo(asset($video));
    $message->setChatId($tgChat->tg_user_id);
    $res = $tgClient->sendVideo($message);

    $file_id = null;
    $thumbnail_id = null;

    if($res['ok'] ?? false){
      $file_id = $res['result']['video']['file_id'] ?? null;
      $thumbnail_id = $res['result']['video']['thumbnail']['file_id'] ?? null;

      if($tgFile){
        $tgFile->update([
            'file_id' => $file_id,
            'thumbnail_id' => $thumbnail_id,
        ]);
      }else{
        TgFile::create([
            'path' => $video,
            'file_id' => $file_id,
            'thumbnail_id' => $thumbnail_id,
        ]);
      }
    }
    return [$file_id, $thumbnail_id];
  }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      if(request()->send_at){
        $send_at = Carbon::createFromFormat('d.m.Y H:i', $request->send_at);
      }else{
        $send_at = now();
      }
      $check_time = DB::table('jobs')
          ->where('queue', 'like', 'tg_queue_%')
          ->where('available_at', '=', $send_at->timestamp)
          ->exists();
      if($check_time){
        return back()->withErrors([
            'Это время рассылки уже занято, выберите другое время'
        ]);
      }
      $filters = $request->filters ? json_decode($request->filters, true) : null;
      $mailing_list = MailingList::create([
          'name' => 'Telegram рассылка '.$send_at->format('d.m.Y H:i'),
          'method' => 'Telegram',
          'message' => $request->message,
          'sending_date' => $send_at->format('Y-m-d H:i')
      ]);
      $users = User::query()
          ->select('id')
          ->filter(new SafeObject($filters ?? []))
          ->whereHas('tgChats', function(Builder $builder){
            $builder->where('active', true);
          })
          ->where('is_subscribed_to_marketing', true)
          ->count();
      if($request->video['file'] ?? false){
        $file = $this->sendTestVideo($request->video['file']);
        if(!($file[0] ?? null)){
          return back()->withErrors([
              'Ошибка загрузки видео в телеграм. Видео должно быть в формате mp4 или mov не более 50mb'
          ]);
        }
      }
      $tgMailing = TelegramMailing::create([
          'message' => $request->message,
          'image' => $request->image['img'] ?? null,
          'video' => $request->video['file'] ?? null,
          'send_at' => $send_at->format('Y-m-d H:i'),
          'filter' => $filters,
          'status' => 'new',
          'data' => [
              'image' => $request->image ?? null,
              'video' => $request->video ?? null,
              'users' => $users,
          ],
          'mailing_id' => $mailing_list->id
      ]);
      return redirect()->route('admin.telegram_mailing.index')->with([
          'success' => 'Рассылка успешно создана'
      ]);
    }
    public function mailing_cancel(Request $request){
      $request->validate([
          'available_at' => ['required']
      ]);
      DB::table('jobs')
          ->where('queue', 'like', 'tg_queue_%')
          ->where('available_at', '=', $request->available_at)
          ->delete();
      return back()->with([
          'success' => 'Рассылка удалена'
      ]);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      $tgMailing = TelegramMailing::findOrFail($id);
      $users = User::query()
          ->select('id')
          ->filter(new SafeObject($tgMailing->filter))
          ->whereHas('tgChats', function(Builder $builder){
            $builder->where('active', true);
          })
          ->where('is_subscribed_to_marketing', true)
          ->count();
      $working_dir = '/shares/tg_mailing';
      if (!file_exists(storage_path('app/public/photos'.$working_dir))) {
        mkdir(storage_path('app/public/photos'.$working_dir), 0777, true);
      }
      $seo = [
          'title' => 'Telegram рассылка '.$tgMailing->send_at->format('d.m.Y H:i')
      ];
      return view('template.admin.telegram_mailing.edit', compact('seo', 'users', 'working_dir', 'tgMailing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      if(request()->send_at){
        $send_at = Carbon::createFromFormat('d.m.Y H:i', $request->send_at);
      }else{
        $send_at = now();
      }
      $check_time = DB::table('jobs')
          ->where('queue', 'like', 'tg_queue_%')
          ->where('available_at', '=', $send_at->timestamp)
          ->exists();
      if($check_time){
        return back()->withErrors([
            'Это время рассылки уже занято, выберите другое время'
        ]);
      }
      $tgMailing = TelegramMailing::findOrFail($id);
      $filters = $tgMailing->filter;
      $mailing_list = $tgMailing->mailing;
      $mailing_list->update([
          'name' => 'Telegram рассылка '.$send_at->format('d.m.Y H:i'),
          'message' => $request->message,
          'sending_date' => $send_at->format('Y-m-d H:i')
      ]);
      $users = User::query()
          ->select('id')
          ->filter(new SafeObject($filters ?? []))
          ->whereHas('tgChats', function(Builder $builder){
            $builder->where('active', true);
          })
          ->where('is_subscribed_to_marketing', true)
          ->count();
      if($request->video['file'] ?? false){
        $file = $this->sendTestVideo($request->video['file']);
        if(!($file[0] ?? null)){
          return back()->withErrors([
              'Ошибка загрузки видео в телеграм. Видео должно быть в формате mp4 или mov не более 50mb'
          ]);
        }
      }
      $tgMailing->update([
          'message' => $request->message,
          'image' => $request->image['img'] ?? null,
          'video' => $request->video['file'] ?? null,
          'send_at' => $send_at->format('Y-m-d H:i'),
          'filter' => $filters,
          'status' => 'new',
          'data' => [
              'image' => $request->image ?? null,
              'video' => $request->video ?? null,
              'users' => $users,
          ],
      ]);
      return redirect()->route('admin.telegram_mailing.index')->with([
          'success' => 'Рассылка успешно обновлена'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
