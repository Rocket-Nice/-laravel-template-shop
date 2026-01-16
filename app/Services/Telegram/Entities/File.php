<?php

namespace App\Services\Telegram\Entities;

class File
{
  /**
   * Unique identifier for the target chat or username of the target channel
   * @var int
   */
  private $chat_id;

  /**
   * Text of the message to be sent, 1-4096 characters after entities parsing
   * @var string
   */
  private $document;

  public function toArray() {
    $vars = get_object_vars($this);
    $result = [];
    foreach($vars as $key => $val){
      $field = [
          'name' => $key
      ];
      if($key == 'document'){
        $field['contents'] = fopen($val, 'r');
        $field['filename'] = basename($val);
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
   * @return string
   */
  public function getDocument(): string
  {
    return $this->document;
  }

  /**
   * @param string $document
   */
  public function setDocument($document)
  {
    $this->document = $document;
  }
}
