<?php

namespace App\Services\Telegram\Entities;

class Photo
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
  private $parse_mode;

  /**
   * Photo to send. Pass a file_id as String to send a photo that exists on
   * the Telegram servers (recommended), pass an HTTP URL as a String for
   * Telegram to get a photo from the Internet, or upload a new photo using
   * multipart/form-data. The photo must be at most 10 MB in size. The photo's
   * width and height must not exceed 10000 in total. Width and height ratio
   * must be at most 20.
   * @var string
   */
  private $photo;

  /*
   * Optional. Caption of the document to be sent, 0-1024 characters after entities parsing
   * @var string
   */
  private $caption;

  public function toArray() {
    $res = array_filter(get_object_vars($this), function($value) {
      return !is_null($value);
    });
    return $res;
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
  public function getParseMode(): string
  {
    return $this->parse_mode;
  }

  /**
   * @param string $parse_mode
   */
  public function setParseMode($parse_mode)
  {
    $this->parse_mode = $parse_mode;
  }
  /**
   * @return string
   */
  public function getPhoto(): string
  {
    return $this->photo;
  }

  /**
   * @param string $photo
   */
  public function setPhoto($photo)
  {
    $this->photo = $photo;
  }
  /**
   * @return string
   */
  public function getCaption(): string
  {
    return $this->caption;
  }

  /**
   * @param string $caption
   */
  public function setCaption($caption)
  {
    $this->caption = $caption;
  }
}
