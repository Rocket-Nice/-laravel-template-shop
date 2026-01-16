<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ImageUploadController extends Controller
{
  public function upload(Request $request)
  {
    $request->validate([
        'files.*' => 'required|mimes:jpeg,png,jpg,gif,heic', // Добавить heic в правила валидации
    ]);
    $files = [];
    if ($request->hasfile('files')) {
      if (!file_exists(storage_path('app/public/product_reviews'))) {
        mkdir(storage_path('app/public/product_reviews'), 0777, true);
      }

      foreach ($request->file('files') as $file) {
        $original_name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        if (mb_strtolower($extension) === 'heic') {
          // Конвертировать HEIC в JPG с помощью Imagick
          $imagick = new \Imagick();
          $imagick->readImage($file->getPathname());
          $imagick->setImageFormat('jpg');
          $jpgPath = $file->getPath() . '/' . pathinfo($file->getFilename(), PATHINFO_FILENAME) . '.jpg';
          $imagick->writeImage($jpgPath);
          $file = new \Illuminate\Http\File($jpgPath);
          $extension = 'jpg'; // Обновить расширение
        }

        $filename = getCode(5) . time() . '.' . $extension;
        $image = Image::make($file);

        // Сжать изображение до 1920 пикселей по максимальной стороне
        if ($image->width() > 1920 || $image->height() > 1920) {
          if ($image->width() > $image->height()) {
            $image->resize(1920, null, function ($constraint) {
              $constraint->aspectRatio();
            });
          } else {
            $image->resize(null, 1920, function ($constraint) {
              $constraint->aspectRatio();
            });
          }
        }

        $image->save(storage_path('app/public/product_reviews/' . $filename));

        // Сохранение имени файла в сессии
        $request->session()->push('uploaded_images', $filename);
        $files[] = [$filename, $original_name];
      }
    }

    return response()->json(['success' => true, 'files' => $files], 200);
  }
}
