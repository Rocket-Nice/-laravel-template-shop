<?php

use Illuminate\Support\Facades\Cache;

function generatePictureHtml(array $imageData, string $alt = ''): string
{
  if(!isset($imageData['image'])){
    $imageData['image'] = $imageData;
  }
  $mediaMap = [
      "200" => "(max-width: 200px)",
      "480" => "(min-width: 201px) and (max-width: 480px)",
      "768" => "(min-width: 480px) and (max-width: 767px)",
      "1200" => "(min-width: 767px) and (max-width: 1199px)",
      "full" => "(min-width: 481px)"
  ];

  $pictureHtml = "";

  // WebP sources
  foreach ($mediaMap as $size => $media) {
    if (isset($imageData['image']["{$size}_webp"])) {
      $srcset = $imageData['image']["{$size}_webp"];
      if (isset($imageData['image']["{$size}@2x_webp"])) {
        $srcset .= ", {$imageData['image']["{$size}@2x_webp"]} 2x";
      }
      $pictureHtml .= "    <source type='image/webp' media='$media' srcset='$srcset'>\n";
    }
  }

  if(!$pictureHtml){
    // JPEG sources
    foreach ($mediaMap as $size => $media) {
      if (isset($imageData['image'][$size])) {
        $srcset = $imageData['image'][$size];
        if (isset($imageData['image']["{$size}@2x"])) {
          $srcset .= ", {$imageData['image']["{$size}@2x"]} 2x";
        }
        $pictureHtml .= "    <source media='$media' srcset='$srcset'>\n";
      }
    }
  }


  // Fallback image
  $imgSrc = $imageData['image']['full'] ?? $imageData['img'] ?? '';
  $pictureHtml .= "    <img src='$imgSrc' class='object-cover' alt='$alt'>\n";
  $pictureHtml .= "";

  return $pictureHtml;
}

function generatePictureHtmlCached(array $imageData, string $alt = '', string $class = ''): string
{
  $cacheKey = 'picture_html_' . md5(serialize($imageData) . $alt . $class);

  return Cache::remember($cacheKey, now()->addHours(24), function () use ($imageData, $alt, $class) {
    return generatePictureHtml($imageData, $alt, $class);
  });
}
