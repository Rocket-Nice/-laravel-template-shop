<?php

namespace App\Services\Telegram\Entities;

class Media
{
  /**
   * Type of the result, must be document
   * @var int
   */
  private $type;

  /**
   * File to send. Pass a file_id to send a file that exists on the Telegram servers (recommended),
   * pass an HTTP URL for Telegram to get a file from the Internet, or pass “attach://<file_attach_name>”
   * to upload a new one using multipart/form-data under <file_attach_name> name.
   * @var string
   */
  private $media;

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
   * @return string
   */
  public function getType(): string
  {
    return $this->type;
  }

  /**
   * @param int $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getMedia(): string
  {
    return $this->media;
  }

  /**
   * @param string $media
   */
  public function setMedia($media)
  {
    $this->media = $media;
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
