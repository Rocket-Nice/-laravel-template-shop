<?php

namespace App\Services\PuzzleService\Entities;

class File
{
  /**
   * Unique identifier for the target chat or username of the target channel
   * @var int
   */
  private $lm_id;
  private $fio;
  private $email;

  /**
   * Text of the message to be sent, 1-4096 characters after entities parsing
   * @var string
   */
  private $image;

  public function toArray() {
    $vars = get_object_vars($this);
    $result = [];
    foreach($vars as $key => $val){
      $field = [
          'name' => $key
      ];
      if($key == 'image'){
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
  public function getLmId(): int
  {
    return $this->lm_id;
  }

  /**
   * @param int $lm_id
   */
  public function setLmId($lm_id)
  {
    $this->lm_id = $lm_id;
  }
  /**
   * @return string
   */
  public function getImage(): string
  {
    return $this->image;
  }

  /**
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getFio(): string
  {
    return $this->fio;
  }

  /**
   * @param string $fio
   */
  public function setFio($fio)
  {
    $this->fio = $fio;
  }
  /**
   * @return string
   */
  public function getEmail(): string
  {
    return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
}
