<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Setting;
use App\Models\TgChat;
use App\Models\TgMessage;
use App\Models\User;
use App\Services\Telegram\Client;
use App\Services\Telegram\Entities\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TeglegramController extends Controller
{
    public function index(){
      $tgClient = new Client();
      $bot = $tgClient->getMe();
      $tgChats = TgChat::query()
          ->addSelect([
              'last_message' => TgMessage::select('time')
                  ->whereColumn('tg_chat_id', 'tg_chats.id')
                  ->latest()
                  ->take(1),
              'user' => User::select('name')
                  ->whereColumn('user_id', 'users.id')
                  ->latest()
                  ->take(1)
          ])
          ->orderByDesc('last_message')
          ->paginate(100);

      $seo = [
          'title' => 'Уведомления в телеграм'
      ];
      return view('template.admin.tg.index', compact( 'seo', 'bot', 'tgChats'));
    }

    public function show(TgChat $tgChat){
      $messages = $tgChat->tgMessages()->orderBy('time', 'desc')->paginate(30);
      $seo = [
          'title' => 'Чат с '.$tgChat->getChatName()
      ];

      $pictureTime = $tgChat->getProfilePictureTime();
      if (!$pictureTime || $pictureTime->diffInHours(now()) > 24) {
        $tgChat->getProfilePicture();
      }

      return view('template.admin.tg.show', compact( 'seo', 'tgChat', 'messages'));
    }
  public function messages(Request $request, TgChat $tgChat){
    $page = $request->page ?? 1;
    $limit = $request->limit ?? 30;
    $messages = $tgChat->tgMessages()->orderBy('time', 'desc');

    $messages = $messages->paginate($limit, ['*'], 'page', $page);

    $messages->getCollection()->transform(function ($item) use ($tgChat) {
      $item->name = $tgChat->getChatName();
      $item->time = $item->time ? $item->time->format('d.m.Y H:i:s') : '';
      $item->image = $tgChat->image;
      if($item->outgoing_message){
        $item->image = asset('apple-touch-icon-144x144.png');
        $item->name = config('app.name');
      }
      return $item;
    });

    $result = $messages->toArray();

    return $result;
  }
  public function send(Request $request, TgChat $tgChat)
  {
    $request->validate([
        'message' => 'required'
    ]);
    $tgMessageText = $request->message;
    $message = $this->userMessage($tgChat, $tgChat->user_id, $tgMessageText);
    $message->name = $tgChat->getChatName();
    $message->time = $message->time ? $message->time->format('d.m.Y H:i:s') : '';
    if($message->outgoing_message){
      $message->image = asset('apple-touch-icon-144x144.png');
      $message->name = config('app.name');
    }
    return [
        'message' => $message->toArray()
    ];
  }
    public function settings(){
      $tg_api = Setting::where('key', 'tg_notifications_bot')->first();
      $seo = [
          'title' => 'Настройки уведомлений в телеграм'
      ];
      return view('template.admin.tg.settings', compact( 'seo', 'tg_api'));
    }

    public function save(Request $request)
    {
      $request->validate([
          'tg_notifications_bot' => 'required'
      ]);
      $setting = Setting::where('key', 'tg_notifications_bot')->first();
      if(!$setting||$setting->value != $request->tg_notifications_bot){
        if(!$setting){
          Setting::create([
              'key' => 'tg_notifications_bot',
              'value' => $request->tg_notifications_bot,
          ]);
        }elseif($setting->value != $request->tg_notifications_bot){
          $setting->update([
              'value' => $request->tg_notifications_bot
          ]);
        }
        Setting::flushQueryCache();

        $tgClient = new Client();
        $tgClient->deleteWebhook();
        $tgClient->setWebhook(route('tg_webhook'));
      }
    }

    public function webhook(Request $request)
    {
      $tgClient = new Client();
      if($request->message){
        if(!isset($request->message['chat']['id'])){
          abort(404);
        }
        $chat_id = $request->message['chat']['id'];
        $tgChat = TgChat::query()->where('tg_user_id', $chat_id)->first();
        if(strpos($request->message['text'], "/start ") === 0){
          $message_text = explode(' ', $request->message['text']);
          $user_uuid = $message_text[1] ?? null;
          if(!$user_uuid){
            $message = new Message();
            $message->setChatId($request->message['chat']['id']);
            $message->setText('Для подключения уведомлений, откройте бот из личного кабинета Le Mousse');
            $tgClient->sendMessage($message);
          }
          $user = User::where('uuid', $user_uuid)->first();
          if($user){
            if(!$tgChat){ // у пользователя уже подключены уведомления
              $tgChat = TgChat::create([
                  'user_id' => $user->id,
                  'tg_user_id' => $chat_id,
                  'username' => $request->message['from']['username'] ?? '',
                  'first_name' => $request->message['from']['first_name'] ?? '',
                  'last_name' => $request->message['from']['last_name'] ?? '',
                  'data' => $request->message,
                  'active' => true,
              ]);
              $tgChat->getProfilePicture();
            }elseif($tgChat->user_id!=$user->id){
              $tgChat->update([
                  'user_id' => $user->id,
                  'username' => $request->message['from']['username'] ?? '',
                  'first_name' => $request->message['from']['first_name'] ?? '',
                  'last_name' => $request->message['from']['last_name'] ?? '',
                  'data' => $request->message,
              ]);
              $pictureTime = $tgChat->getProfilePictureTime();
              if (!$pictureTime || $pictureTime->diffInHours(now()) > 24) {
                $tgChat->getProfilePicture();
              }
            }
            $tgMessageText = 'Вы успешно подписались на уведомления от Le Mousse. Спасибо!';
            $this->userMessage($tgChat, $tgChat->user_id, $tgMessageText);
          }
        }
        if($tgChat){
          $tgMssage = TgMessage::query()
              ->where('tg_chat_id', $tgChat->id)
              ->where('tg_message_id', $request->message['message_id'])
              ->first();
          if(!$tgMssage){
            $tgMessage = TgMessage::create([
                'tg_chat_id' => $tgChat->id,
                'user_id' => $tgChat->user_id,
                'tg_message_id' => $request->message['message_id'],
                'text' => $request->message['text'],
                'time' => date('Y-m-d H:i:s', $request->message['date']),
                'data' => $request->message,
                'outgoing_message' => false
            ]);
          }
        }

      }elseif($request->my_chat_member && $request->my_chat_member['new_chat_member']['user']['id'] == 6786069216){
        $chat_id = $request->my_chat_member['chat']['id'];
        $tgChat = TgChat::query()->where('tg_user_id', $chat_id)->first();
        if($tgChat){
          if($request->my_chat_member['new_chat_member']['status'] == 'member' && $request->my_chat_member['old_chat_member']['status'] != 'member') {
            $tgChat->update([
                'active' => true
            ]);
            $tgMessageText = 'Вы успешно подписались на уведомления от Le Mousse. Спасибо!';
            $this->userMessage($tgChat, $tgChat->user_id, $tgMessageText);
          }else{
            $tgChat->update([
                'active' => false
            ]);
          }
        }
      }
    }


    public function userMessage(TgChat $tgChat, $user_id, $text)
    {
      $tgClient = new Client();

      $tgMessage = TgMessage::create([
          'tg_chat_id' => $tgChat->id,
          'user_id' => $user_id,
          'text' => $text,
          'time' => now()->format('Y-m-d H:i:s'),
          'outgoing_message' => true
      ]);
      $message = new Message();
      $message->setChatId($tgChat->tg_user_id);
      $message->setText($text);
      $res = $tgClient->sendMessage($message);

      if(isset($res['ok'])&&$res['ok']==1){
        $tgMessage->update([
            'tg_message_id' => $res['result']['message_id'],
            'text' => nl2br($res['result']['text']),
            'time' => date('Y-m-d H:i:s', $res['result']['date']),
            'data' => $res['result'],
            'delivered' => 1,
        ]);
        if(!$tgChat->active){
          $tgChat->update([
              'active' => true
          ]);
        }
      }elseif(isset($res['error_code'])&&$res['error_code']==403){
        $tgChat->update([
            'active' => false
        ]);
      }
      return $tgMessage;
    }
}
