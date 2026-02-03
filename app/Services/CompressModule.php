<?php

namespace App\Services;

use App\Models\BlogArticle;
use App\Models\Content;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Spatie\PdfToImage\Pdf;

class CompressModule
{
  static public function compressArticleImages($id){
    $article = BlogArticle::find($id);

    $data_content = $article->data_content['_request'] ?? null;
    if ($data_content) {
      foreach($data_content as $key => $content){
        if(!is_array($content)){
          continue;
        }
        foreach($content as $index => $slide) {
          if (isset($slide['image']['img'])&&!empty($slide['image']['img'])){
            $compressedImages = CompressModule::compressImage($slide['image']['img'], [480,768,1200,1920], 1280);
            $data_content[$key][$index]['image']['size'] = $compressedImages;
            $data_content[$key][$index]['image']['maxWidth'] = $data_content[$key][$index]['image']['maxWidth'] ?? 1000;
          }elseif(isset($slide['image'])&&is_array($slide['image'])){
            foreach($slide['image'] as $key_img => $img){
              $compressedImages = CompressModule::compressImage($img['img'], [480,768,1200,1920], 1280);
              $slide['image'][$key_img]['size'] = $compressedImages;
              $slide['image'][$key_img]['maxWidth'] = $data_content[$key][$index]['image']['maxWidth'] ?? 1000;
            }
            $data_content[$key][$index]['image'] = $slide['image'];
          }
        }
      }
      $article_prams['data_content'] = $data_content;
    }
    $old = $article->toArray();
    if(!empty($article_prams)){
      $article->update($article_prams);
    }
    BlogArticle::flushQueryCache();
    return true;

  }
  static public function compressContentImages($id)
  {
    $content = Content::find($id);
    Log::debug('content '.$id);
    $images = $content->image_data['_request'] ?? null;
    Log::debug(print_r($images, true));
    $content_prams = [];
    if ($images) {
      foreach($images as $key => $value){

        if(isset($value['img'])&&!empty($value['img'])) {
          $compressedImages = CompressModule::compressImage($value['img'], [480,768,1200,1920], $value['maxWidth'] ?? 1000);
          $images[$key]['size'] = $compressedImages;
        }
      }
      $content_prams['image_data'] = $images;
    }
    $carousels = $content->carousel_data['_request'] ?? null;
    if ($carousels) {
      foreach($carousels as $key => $carousel){
        if(in_array($key, [ 'certificates',  'certificatesRed',  'certificatesGreen', 'certificatesKids'])&&is_array($carousel)) {
          foreach($carousel as $index => $slide){
            if(isset($slide['file'])){
              $path = parse_url($slide['file'], PHP_URL_PATH);
              $relativePath = ltrim(str_replace('/storage/', '', $path), '/');

              // Проверить наличие исходного файла
              if (!Storage::disk('public')->exists($relativePath)) {
                throw new Exception('Исходное изображение не найдено. '.$relativePath);
              }
              $directory = pathinfo($relativePath, PATHINFO_DIRNAME);
              $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
              $filenameWithoutExtension = pathinfo($relativePath, PATHINFO_FILENAME);

              $newRelativePath = "{$directory}/pdf_thumb/{$filenameWithoutExtension}.jpg";

              if(!Storage::disk('public')->exists($newRelativePath)) {
                if ($extension === 'pdf') {
                  $pdf = new Pdf(storage_path('app/public/'.$relativePath));
                  if (!file_exists(storage_path('app/public/'.$directory.'/pdf_thumb'))) {
                    mkdir(storage_path('app/public/'.$directory.'/pdf_thumb'), 0777, true);
                  }
                  $img_path = storage_path('app/public/'.$newRelativePath);
                  $pdf->setPage(1)->saveImage($img_path);

                  $previewImage = Image::make($img_path);
                  $previewImage->resize(320, null, function ($constraint) {
                    $constraint->aspectRatio();
                  });
                  Storage::disk('public')->put($newRelativePath, (string) $previewImage->encode(null, 75));
                }elseif(in_array($extension, ['jpeg', 'jpg', 'png'])){
                  $originalImage = Image::make(storage_path('app/public/'.$relativePath));
                  $compressedImage = clone $originalImage;
                  $compressedImage->resize(320, null, function ($constraint) {
                    $constraint->aspectRatio();
                  });
                  Storage::disk('public')->put($newRelativePath, (string) $compressedImage->encode(null, 75));
                }
              }
              $carousels[$key][$index]['thumb'] = asset('storage/'.$newRelativePath);
            }elseif(isset($slide['files'])){
              foreach($slide['files'] as $i => $file){
                $path = parse_url($file['file'], PHP_URL_PATH);
                $relativePath = ltrim(str_replace('/storage/', '', $path), '/');

                // Проверить наличие исходного файла
                if (!Storage::disk('public')->exists($relativePath)) {
                  throw new Exception('Исходное изображение не найдено. '.$relativePath);
                }
                $directory = pathinfo($relativePath, PATHINFO_DIRNAME);
                $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
                $filenameWithoutExtension = pathinfo($relativePath, PATHINFO_FILENAME);

                $newRelativePath = "{$directory}/pdf_thumb/{$filenameWithoutExtension}.jpg";

                if(!Storage::disk('public')->exists($newRelativePath)) {
                  if ($extension === 'pdf') {
                    $pdf = new Pdf(storage_path('app/public/'.$relativePath));
                    if (!file_exists(storage_path('app/public/'.$directory.'/pdf_thumb'))) {
                      mkdir(storage_path('app/public/'.$directory.'/pdf_thumb'), 0777, true);
                    }
                    $img_path = storage_path('app/public/'.$newRelativePath);
                    $pdf->setPage(1)->saveImage($img_path);

                    $previewImage = Image::make($img_path);
                    $previewImage->resize(320, null, function ($constraint) {
                      $constraint->aspectRatio();
                    });
                    Storage::disk('public')->put($newRelativePath, (string) $previewImage->encode(null, 75));
                  }elseif(in_array($extension, ['jpeg', 'jpg', 'png'])){
                    $originalImage = Image::make(storage_path('app/public/'.$relativePath));
                    $compressedImage = clone $originalImage;
                    $compressedImage->resize(320, null, function ($constraint) {
                      $constraint->aspectRatio();
                    });
                    Storage::disk('public')->put($newRelativePath, (string) $compressedImage->encode(null, 75));
                  }
                }
                $carousels[$key][$index]['files'][$i]['thumb'] = asset('storage/'.$newRelativePath);
              }
            }

          }
        }else{
          foreach($carousel as $index => $slide) {
            if (isset($slide['image']['img'])&&!empty($slide['image']['img'])){
              $compressedImages = CompressModule::compressImage($slide['image']['img'], [480,768,1200,1920], 1000);
              $carousels[$key][$index]['image']['size'] = $compressedImages;
              $carousels[$key][$index]['image']['maxWidth'] = 1000;
            }elseif(isset($slide['image'])&&is_array($slide['image'])){
              foreach($slide['image'] as $key_img => $img){
                $compressedImages = CompressModule::compressImage($img['img'], [480,768,1200,1920], 1000);
                $slide['image'][$key_img]['size'] = $compressedImages;
                $slide['image'][$key_img]['maxWidth'] = 1000;
              }
              $carousels[$key][$index]['image'] = $slide['image'];
            }
          }
        }
      }

      $content_prams['carousel_data'] = $carousels;
    }
    $old = $content->toArray();
    if(!empty($content_prams)){
      $content->update($content_prams);
    }
    Content::flushQueryCache();
    return true;
  }
  static public function compressProductImages($id)
  {
    $product = Product::find($id);
    $product_params = [];
    $product_cards = $product->style_cards['_request'] ?? null;
    if($product_cards !== null){
      $card_i = 1;
      $product_cards_prepare = [];
      foreach($product_cards as $key => $card){
        if($card['card_style']=='card-style-5'){
          $style = '';
          if($card['color'] ?? false){
            $style .= 'color: '.$card['color'].';';
          }
          if($card['background'] ?? false){
            $style .= 'background: '.$card['background'].';';
          }
          $product_cards[$key]['style'] = $style;
        }
        if (isset($card['img'])&&!empty($card['img'])){
          $compressedImages = self::compressImage($card['img'], [200,480,768,1200,1920], $card['maxWidth'] ?? 960);
          $product_cards[$key]['image'] = $compressedImages;
        }
        if(isset($card['fields'])&&!empty($card['fields'])){
          foreach($card['fields'] as $i => $field){
            $style = '';
            if($field['vertical-align'] ?? false){
              if($field['vertical-align'] == 'v-pos-top'){
                $style .= 'top: '.($field['vertical-align-value'] ?? 0).'%;bottom: auto;';
              }elseif($field['vertical-align'] == 'v-pos-bottom'){
                $style .= 'bottom: '.($field['vertical-align-value'] ?? 0).'%;top: auto;';
              }
            }
            $product_cards[$key]['fields'][$i]['style'] = $style;
          }
        }
        if(isset($card['text'])&&!empty($card['text'])){
          $style = '';
          if($card['vertical-align'] ?? false){
            if($card['vertical-align'] == 'v-pos-top'){
              $style .= 'top: '.($card['vertical-align-value'] ?? 0).'%;bottom: auto;';
            }elseif($card['vertical-align'] == 'v-pos-bottom'){
              $style .= 'bottom: '.($card['vertical-align-value'] ?? 0).'%;top: auto;';
            }
          }
          if($card['align'] ?? false){
            if($card['align'] == 'h-pos-left'){
              $style .= 'left: '.($card['align-value'] ?? 0).'%;right: auto;';
            }elseif($card['align'] == 'h-pos-right'){
              $style .= 'right: '.($card['vertical-align-value'] ?? 0).'%;left: auto;';
            }
          }

          if($card['color'] ?? false){
            $style .= 'color: '.$card['color'].';';
          }
          if($card['background'] ?? false){
            $style .= 'background: '.$card['background'].';';
          }
          $product_cards[$key]['text-style'] = $style;
        }
        if(isset($card['big-text'])&&!empty($card['big-text'])){
          $style = '';
          if($card['big-text-vertical-align'] ?? false){
            if($card['big-text-vertical-align'] == 'v-pos-top'){
              $style .= 'top: '.($card['big-text-vertical-align-value'] ?? 0).'%;bottom: auto;';
            }elseif($card['big-text-vertical-align'] == 'v-pos-bottom'){
              $style .= 'bottom: '.($card['big-text-vertical-align-value'] ?? 0).'%;top: auto;';
            }
          }
          if($card['big-text-align'] ?? false){
            if($card['big-text-align'] == 'h-pos-left'){
              $style .= 'left: '.($card['big-text-align-value'] ?? 0).'%;right: auto;';
            }elseif($card['big-text-align'] == 'h-pos-right'){
              $style .= 'right: '.($card['big-text-vertical-align-value'] ?? 0).'%;left: auto;';
            }
          }

          if($card['small-text-color'] ?? false){
            $style .= 'color: '.$card['small-text-color'].';';
          }
          $product_cards[$key]['big-text-style'] = $style;
        }
        if(isset($card['small-text'])&&!empty($card['small-text'])){
          $style = '';
          if($card['small-text-vertical-align'] ?? false){
            if($card['small-text-vertical-align'] == 'v-pos-top'){
              $style .= 'top: '.($card['small-text-vertical-align-value'] ?? 0).'%;bottom: auto;';
            }elseif($card['small-text-vertical-align'] == 'v-pos-bottom'){
              $style .= 'bottom: '.($card['small-text-vertical-align-value'] ?? 0).'%;top: auto;';
            }
          }
          if($card['small-text-align'] ?? false){
            if($card['small-text-align'] == 'h-pos-left'){
              $style .= 'left: '.($card['small-text-align-value'] ?? 0).'%;right: auto;';
            }elseif($card['small-text-align'] == 'h-pos-right'){
              $style .= 'right: '.($card['small-text-vertical-align-value'] ?? 0).'%;left: auto;';
            }
          }
          if($card['small-text-color'] ?? false){
            $style .= 'color: '.$card['small-text-color'].';';
          }
          $product_cards[$key]['small-text-style'] = $style;
        }
        $product_cards_prepare[$card_i] = $product_cards[$key];
        $card_i++;
      }
      if(!empty($product_cards_prepare)){
        $product_cards = $product_cards_prepare;
      }
      $product_params['style_cards'] = $product_cards;
    }

    $product_page = $product->style_page['_request'] ?? null;
    if($product_page){
      foreach($product_page as $key => $block){
        if (isset($block['img'])&&!empty($block['img'])){
          $compressedImages = self::compressImage($block['img'], [200,480,768,1200,1920], $block['maxWidth'] ?? null);
          $product_page[$key]['image'] = $compressedImages;
        }
      }
      if(isset($product_page['celebrities'])){
        foreach($product_page['celebrities'] as $index => $slide) {
          if (isset($slide['image']['img'])&&!empty($slide['image']['img'])){
            $compressedImages = CompressModule::compressImage($slide['image']['img'], [480,768,1200,1920], 1000);
            $product_page['celebrities'][$index]['image']['size'] = $compressedImages;
            $product_page['celebrities'][$index]['image']['maxWidth'] = 1000;
          }elseif(isset($slide['image'])&&is_array($slide['image'])){
            foreach($slide['image'] as $key_img => $img){
              $compressedImages = CompressModule::compressImage($img['img'], [480,768,1200,1920], 1000);
              $slide['image'][$key_img]['size'] = $compressedImages;
              $slide['image'][$key_img]['maxWidth'] = 1000;
            }
            $product_page['celebrities'][$index]['image'] = $slide['image'];
          }
        }
      }
      if(isset($product_page['mainVideo']['file'])){
        $video = $product_page['mainVideo']['file'];
        $path = parse_url($video, PHP_URL_PATH);
        // Удаляем префикс '/storage' из пути
        $open = str_replace('/storage', '', $path);
        // Получаем расширение файла
        $extension = pathinfo($open, PATHINFO_EXTENSION);
        // Если расширение .mov, заменяем его на .mp4
        if(mb_strtolower($extension) != 'mp4') {
          $save = preg_replace('/\.mov$/i', '.mp4', $open);
          if(!file_exists(storage_path('app/public'.$save))){
            $product_page = self::videoConversion($open, $save, $product_page);
          }else{
            $extension = pathinfo($save, PATHINFO_EXTENSION);
            $product_page['mainVideo'][$extension] = asset('storage'.$save);
          }
        }else{
          $product_page['mainVideo']['mp4'] = $product_page['mainVideo']['file'];
        }
      }
      $product_params['style_page'] = $product_page;
    }
    if(!empty($product_params)){
      $product->update($product_params);
    }


    Product::flushQueryCache();
    return true;
  }

  static public function videoConversion($open, $save, $product_page){
    $extension = pathinfo($save, PATHINFO_EXTENSION);
    // echo $extension.'<br/>';
    $ffmpeg = FFMpeg::fromDisk('public')->open($open);

    if($extension == 'mp4'){
      $format = new \FFMpeg\Format\Video\X264();
      $format->setKiloBitrate(4000); // Установите битрейт видео
      $ffmpeg->addFilter('-an') // Этот фильтр удаляет аудио
          ->addFilter('-preset', 'veryslow')
          ->addFilter('-crf', '18');
    }elseif($extension == 'webm'){
      $format = new \FFMpeg\Format\Video\WebM();
      $format->setVideoCodec('libvpx')
          ->setKiloBitrate(4000);;
    }else{
      return false;
    }
    if(isset($product_page['mainVideo'][$extension])&&$product_page['mainVideo'][$extension] == asset('storage'.$save)){
      $product_page['mainVideo'][$extension] = asset('storage'.$save);
      return $product_page;
    }
    $ffmpeg->export()
        ->toDisk('public') // также замените 'disk_name', если вы хотите сохранить файл не в локальной файловой системе
        ->inFormat($format)
        ->save($save);

    $product_page['mainVideo'][$extension] = asset('storage'.$save);
    return $product_page;
  }
  static public function compressImage($url, array $sizes, $maxWidth = null)
  {
    Log::debug('$maxWidth '.$maxWidth);
    $format = 'webp';
    $compressedUrls = [];
    // Извлечь путь из URL
    $path = parse_url($url, PHP_URL_PATH);

    $relativePath = ltrim(str_replace('/storage/', '', $path), '/');

    if(preg_match('/\s|[\p{Cyrillic}]/u', $relativePath) > 0){
      $relativePath = self::copyImageFile(storage_path('app/public/'.$relativePath));
      if (preg_match('#storage/app/public/(.*)#', $relativePath, $matches)) {
        $relativePath = $matches[1];
      }
    }

    // Проверить наличие исходного файла
    if (!Storage::disk('public')->exists($relativePath)) {
      //throw new Exception('Исходное изображение не найдено. '.$relativePath);
      $originalImage = Image::make($url)->orientate();
    } else {
        $originalImage = Image::make(Storage::disk('public')->path($relativePath))->orientate();
    }

    $originalWidth = $originalImage->width();

    if ($maxWidth && $originalWidth > $maxWidth) {
      $sizes = array_filter($sizes, function ($size) use ($maxWidth) {
        return $size <= $maxWidth;
      });
      $originalImage->resize($maxWidth, null, function ($constraint) {
        $constraint->aspectRatio();
      });
      $originalWidth = $maxWidth; // обновляем значение ширины
    }

    // Для каждого размера проверяем и создаем новое изображение
    foreach ($sizes as $width) {
      if ($originalWidth >= $width) {
        $resizedImage = clone $originalImage; // Клонируем, чтобы не изменять исходное изображение
        $resizedImage->resize($width, null, function ($constraint) {
          $constraint->aspectRatio();
        });

        $directory = pathinfo($relativePath, PATHINFO_DIRNAME);
        $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
        $filenameWithoutExtension = pathinfo($relativePath, PATHINFO_FILENAME);

        $suffix = "-{$width}";
        $newRelativePath = "{$directory}/compressed/{$filenameWithoutExtension}{$suffix}.{$extension}";

        if (!Storage::disk('public')->exists($newRelativePath)) {
          Storage::disk('public')->put($newRelativePath, (string) $resizedImage->encode(null, 97));
        }

        $compressedUrls[$width] = asset("storage/{$newRelativePath}");

        // webp
        $newWebpPath = "{$directory}/compressed/{$filenameWithoutExtension}{$suffix}.{$format}";
        if (!Storage::disk('public')->exists($newWebpPath)) {
          Storage::disk('public')->put($newWebpPath, (string) $resizedImage->encode($format, 97));
        }
        $compressedUrls["{$width}_webp"] = asset("storage/{$newWebpPath}");

        // Создание изображения для Retina-экранов (x2)
        if ($originalWidth >= $width * 2) {
          $resizedRetinaImage = clone $originalImage;
          $resizedRetinaImage->resize($width * 2, null, function ($constraint) {
            $constraint->aspectRatio();
          });

          $suffixRetina = "{$suffix}@2x";
          $newRetinaPath = "{$directory}/compressed/{$filenameWithoutExtension}{$suffixRetina}.{$extension}";

          if (!Storage::disk('public')->exists($newRetinaPath)) {
            Storage::disk('public')->put($newRetinaPath, (string) $resizedRetinaImage->encode(null, 97));
          }

          $compressedUrls[$width.'@2x'] = asset("storage/{$newRetinaPath}");

          // webp
          $newRetinaWebpPath = "{$directory}/compressed/{$filenameWithoutExtension}{$suffixRetina}.{$format}";
          if (!Storage::disk('public')->exists($newRetinaWebpPath)) {
            Storage::disk('public')->put($newRetinaWebpPath, (string) $resizedRetinaImage->encode($format, 97));
          }
          $compressedUrls["{$width}@2x_webp"] = asset("storage/{$newRetinaWebpPath}");
        }

      }
    }

    $directory = pathinfo($relativePath, PATHINFO_DIRNAME);
    $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
    $filenameWithoutExtension = pathinfo($relativePath, PATHINFO_FILENAME);
    $newFullPath = "{$directory}/compressed/{$filenameWithoutExtension}.{$extension}";

    // Сохраняем сжатое изображение оригинального размера, если оно еще не существует
    if (!Storage::disk('public')->exists($newFullPath)) {
      Storage::disk('public')->put($newFullPath, (string) $originalImage->encode(null, 97));
    }

    // Добавляем ссылку на сжатое изображение оригинального размера в массив URL-ов
    $compressedUrls['full'] = asset("storage/{$newFullPath}");

    $compressedCopyPath = "{$directory}/compressed/{$filenameWithoutExtension}-c.{$extension}";
    if (!Storage::disk('public')->exists($compressedCopyPath)) {
      $compressedImage = clone $originalImage;
      $compressedImage->resize($originalWidth / 2, null, function ($constraint) {
        $constraint->aspectRatio();
      })->blur(100);
      Storage::disk('public')->put($compressedCopyPath, (string) $compressedImage->encode(null, 50));
    }

    $compressedUrls['minimal_quality'] = asset("storage/{$compressedCopyPath}");

    $newFullWebpPath = "{$directory}/compressed/{$filenameWithoutExtension}.{$format}";
    if (!Storage::disk('public')->exists($newFullWebpPath)) {
      Storage::disk('public')->put($newFullWebpPath, (string) $originalImage->encode($format, 97));
    }
    $compressedUrls['full_webp'] = asset("storage/{$newFullWebpPath}");

    $compressedCopyWebpPath = "{$directory}/compressed/{$filenameWithoutExtension}-c.{$format}";
    if (!Storage::disk('public')->exists($compressedCopyWebpPath)) {
      $compressedImage = clone $originalImage;
      $compressedImage->resize($originalWidth / 2, null, function ($constraint) {
        $constraint->aspectRatio();
      })->blur(100);
      Storage::disk('public')->put($compressedCopyWebpPath, (string) $compressedImage->encode($format, 50));
    }
    $compressedUrls['minimal_quality_webp'] = asset("storage/{$compressedCopyWebpPath}");

    return $compressedUrls;
  }

  static private  function copyImageFile($path) {
    // Получение информации о файле
    $info = pathinfo($path);
    $filename = $info['filename'];
    $extension = $info['extension'];
    $dirname = $info['dirname'];

    // Транслитерация и удаление пробелов
    $newFilename = self::cyrillicToLatin($filename);
    $newFilename = str_replace(' ', '_', $newFilename);

    // Полный путь к новому файлу
    $newPath = $dirname . DIRECTORY_SEPARATOR . $newFilename . '.' . $extension;

    // Копирование файла, если он еще не был скопирован
    if ($path !== $newPath && !file_exists($newPath)) {
      copy($path, $newPath);
    }
    return $newPath;
  }

  static private function cyrillicToLatin($text) {
    $translitTable = [
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
        'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K',
        'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R',
        'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e',
        'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k',
        'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya'
    ];

    return str_replace(array_keys($translitTable), array_values($translitTable), $text);
  }
}
