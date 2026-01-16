<?php

namespace App\Services\Telegram\Entities;

use Illuminate\Support\Str;

class Video
{
  /**
   * Unique identifier for the target chat or username of the target channel
   * @var bool
   */
  private $attach = true;
  /**
   * Unique identifier for the target chat or username of the target channel
   * @var int
   */
  private $chat_id;
  /**
   * Mode for parsing entities in the video caption. See formatting options for more details.
   * @var string
   */
  private $parse_mode;

  /**
   * Video to send. Pass a file_id as String to send a video that exists on the Telegram servers (recommended),
   * pass an HTTP URL as a String for Telegram to get a video from the Internet,
   * or upload a new video using multipart/form-data.
   * @var string
   */
  private $video;

  /**
   * Thumbnail of the file sent; can be ignored if thumbnail generation for the file is supported server-side.
   * The thumbnail should be in JPEG format and less than 200 kB in size.
   * A thumbnail's width and height should not exceed 320.
   * Ignored if the file is not uploaded using multipart/form-data.
   * Thumbnails can't be reused and can be only uploaded as a new file,
   * so you can pass “attach://<file_attach_name>” if the thumbnail was uploaded
   * using multipart/form-data under <file_attach_name>
   * @var string
   */
  private $thumbnail;

  /*
   * Video width
   * @var integer
   */
  private $width;

  /*
   * Video height
   * @var integer
   */
  private $height;

  /*
   * Duration of sent video in seconds
   * @var integer
   */
  private $duration;
  /*
   * Optional. Caption of the document to be sent, 0-1024 characters after entities parsing
   * @var string
   */
  private $caption;

  /*
   * Pass True if the uploaded video is suitable for streaming
   * @var bool
   */
  private $supports_streaming = false;

  private function extractThumbnail($videoPath, $outputImagePath, $time = '00:00:01') {
    // Убедись, что ffmpeg установлен и доступен
    $cmd = "ffmpeg -y -i " . escapeshellarg($videoPath) . " -ss {$time} -vframes 1 -vf scale=320:-1 -q:v 2 " . escapeshellarg($outputImagePath) . " 2>&1";
    $output = shell_exec($cmd);

    return file_exists($outputImagePath);
  }
  private function getVideoMetadata($filePath) {
    $escapedPath = escapeshellarg($filePath);

    // Команда ffprobe: получаем ширину, высоту и длительность
    $cmd = "ffprobe -v error " .
        "-select_streams v:0 " .
        "-show_entries stream=width,height " .
        "-show_entries format=duration " .
        "-of default=noprint_wrappers=1:nokey=1 $escapedPath";

    $output = shell_exec($cmd);

    if (!$output) {
      return false;
    }

    $lines = explode("\n", trim($output));

    if (count($lines) < 3) {
      return false; // недостаточно данных
    }

    return [
        'width'     => (int) $lines[0],
        'height'    => (int) $lines[1],
        'duration'  => (float) $lines[2], // в секундах
    ];
  }
  public function toArray() {
    if($this->attach){
      $vars = get_object_vars($this);
      $result = [];
      foreach($vars as $key => $val){
        if($key == 'attach') {
          continue;
        }
        $field = [
            'name' => $key
        ];
        if($key == 'video' || $key == 'thumbnail'){
          $field['contents'] = fopen($val, 'r');
          $field['filename'] = basename($val);
        }else{
          $field['contents'] = $val;
        }
        $result[] = $field;
      }
    }else{
      $result = array_filter(get_object_vars($this), function($value) {
        return !is_null($value);
      });
      unset($result['attach']);
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
  public function getVideo(): string
  {
    return $this->video;
  }

  /**
   * @param string $video
   */
  public function setVideo($video)
  {
    $this->video = $video;
    if ($this->attach) {
      $params = $this->getVideoMetadata($video);
      $this->width = $params['width'];
      $this->height = $params['height'];
      $this->duration = $params['duration'];
      $thumb_name = Str::random(20) . '.jpeg';
      $thumb = $this->extractThumbnail($video, $thumb_name);
      if($thumb){
        $this->thumbnail = $thumb_name;
      }
    }
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
  /**
   * @return string
   */
  public function getThumbnail(): string
  {
    return $this->thumbnail;
  }

  /**
   * @param string $thumbnail
   */
  public function setThumbnail($thumbnail)
  {
    $this->thumbnail = $thumbnail;
  }
  /**
   * @return bool
   */
  public function getAttach(): bool
  {
    return $this->attach;
  }

  /**
   * @param bool $attach
   */
  public function setAttach($attach)
  {
    $this->attach = $attach;
  }
  /**
   * @return bool
   */
  public function getSupportsStreaming(): bool
  {
    return $this->supports_streaming;
  }

  /**
   * @param bool $supports_streaming
   */
  public function setSupportsStreaming($supports_streaming)
  {
    $this->supports_streaming = $supports_streaming;
  }
}
