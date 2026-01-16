<?php

namespace App\Services\Telegram\Entities;

class Message
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
  private $text;

  /**
   * Text of the message to be sent, 1-4096 characters after entities parsing
   * @var string
   */
  private $parse_mode;

  public function toArray() {
    return get_object_vars($this);
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
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
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
}
