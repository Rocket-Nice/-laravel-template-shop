<?php

namespace App\Models;

use App\Services\Telegram\Client;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class TgChat extends Model
{
    use HasFactory, Notifiable;
  public function user() {
    return $this->belongsTo('App\Models\User');
  }

  public function tgMessages() {
    return $this->hasMany('App\Models\TgMessage');
  }

  public function getChatName()
  {
    if($this->first_name || $this->last_name){
      return trim($this->first_name.' '.$this->last_name);
    }else{
      return $this->username;
    }
  }

  public function getProfilePicture()
  {
    $tgClient = new Client();
    $profile_pictures = $tgClient->getUserProfilePhotos($this->tg_user_id);

    if(isset($profile_pictures['result']['photos'])&&!empty($profile_pictures['result']['photos'])){
      $file = $tgClient->getFile($profile_pictures['result']['photos'][0][0]['file_id']);

      if($file['ok'] ?? false && $file['result']['file_path'] ?? false){
        $tgClientFile = new Client('https://api.telegram.org/file/bot');
        $download = $tgClientFile->downloadFile($file['result']['file_path'], 'tg_users/'.$this->id);
        if($download){
          $link = storageToAsset($download);
          $this->update([
              'image' => $link
          ]);
        }
      }
    }
  }
  public function getProfilePictureTime()
  {
    preg_match('/file(\d+)\.jpg$/', $this->image, $matches);

    if (!empty($matches)) {
      $timestamp = $matches[1]; // Извлечение временной метки

      // Преобразование Unix временной метки в читаемый формат
      $dateTime = Carbon::createFromTimestamp($timestamp);

      return $dateTime;
    } else {
      return null;
    }
  }
    protected $casts = [
        'data' => 'array'
    ];

    protected $fillable = [
        'user_id',
        'tg_user_id',
        'username',
        'first_name',
        'last_name',
        'image',
        'data',
        'active',
    ];

  public function routeNotificationForTelegram()
  {
    return $this->tg_user_id; // Атрибут, где хранится Telegram ID пользователя
  }
}
