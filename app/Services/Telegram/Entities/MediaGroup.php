<?php

namespace App\Services\Telegram\Entities;

class MediaGroup
{
  /**
   * Unique identifier for the target chat or username of the target channel
   * @var int
   */
  private $chat_id;

  /**
   * A JSON-serialized array describing messages to be sent, must include 2-10 items
   * @var array
   */
  private $media;

  public function toArray() {
    $vars = get_object_vars($this);
    $result = [];
    $mediaGroup = [];
    foreach($vars as $key => $val){
      $field = [
          'name' => $key
      ];
      if($key == 'media'){
        foreach($val as $i => $file){
          $file_id = 'file'.$i;
          $file_obj = [
            'name' => $file_id,
            'contents' => fopen($file['media'], 'r'),
            'filename' => basename($file['media'])
          ];
          $file['media'] = 'attach://'.$file_id;
          $mediaGroup[] = $file;
          $result[] = $file_obj;
        }
        $field['contents'] = json_encode($mediaGroup);
      }else{
        $field['contents'] = $val;
      }
      $result[] = $field;
    }
    return $result;
  }
  /**
   * @return int
   */
  public function getChatId(): int
  {
    return $this->chat_id;
  }

  /**
   * @param int $chat_id
   */
  public function setChatId($chat_id)
  {
    $this->chat_id = $chat_id;
  }
  /**
   * @return array
   */
  public function getMedia(): array
  {
    return $this->media;
  }

  /**
   * @param array $media
   */
  public function setMedia($media)
  {
    $this->media = $media;
  }
}
