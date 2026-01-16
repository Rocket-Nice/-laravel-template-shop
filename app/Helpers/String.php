<?php

use App\Models\Status;
use Carbon\Carbon;
use Intervention\Image\Facades\Image as Img;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

function randomFloat($min = 3, $max = 5) {
  $range = $max - $min;
  $number = $min + $range * (mt_rand(0, 10000) / 10000);
  return round($number, 2);  // округляем до двух десятичных знаков
}
function get_size( $bytes )
{
  if ( $bytes < 1000 * 1024 ) {
    return number_format( $bytes / 1024, 2 ) . " KB";
  }
  elseif ( $bytes < 1000 * 1048576 ) {
    return number_format( $bytes / 1048576, 2 ) . " MB";
  }
  elseif ( $bytes < 1000 * 1073741824 ) {
    return number_format( $bytes / 1073741824, 2 ) . " GB";
  }
  else {
    return number_format( $bytes / 1099511627776, 2 ) . " TB";
  }
}
function btw($b1) {
  $b1 = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $b1);
  $b1 = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $b1);
  return $b1;
}
function toDatepickerFormat($date = null){
  if(!$date){
    return null;
  }
  $date = Carbon::createFromFormat('Y-m-d', $date);
  if ($date) {
    return $date->format('d.m.Y');
  } else {
    return null;
  }
}
function generateSVG($percentage, $diameter = 58, $stroke = 5, $class = '', $color = '#FFFFFF') {
  // Расчет радиуса круга
  $radius = $diameter / 2 - $stroke / 2; // 2.5 - половина толщины обводки

  // Расчет длины окружности
  $circleLength = 2 * M_PI * $radius;

  // Расчет длины обводки, соответствующей процентному заполнению
  $offset = $circleLength - ($percentage / 100) * $circleLength;

  $cx = $radius + $stroke / 2;
  $cy = $radius + $stroke / 2;

  if(!$class){
    $class = "absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-[{$diameter}px] h-[{$diameter}px] observed";
  }
  // Генерация SVG
  $svg = <<<EOT
<svg class="$class" viewBox="0 0 $diameter $diameter" xmlns="http://www.w3.org/2000/svg">
    <circle cx="$cx" cy="$cy" r="$radius" stroke="$color" stroke-opacity="0.24" stroke-width="$stroke" fill="none" />
    <circle cx="$cx" cy="$cy" r="$radius" stroke="$color" stroke-width="$stroke" fill="none" stroke-dasharray="$circleLength" stroke-dashoffset="$circleLength" transform="rotate(-90, $cx, $cy)" data-circle-length="$circleLength" data-percentage="$percentage" class="progressCircle">
        <animate attributeName="stroke-dashoffset" from="$circleLength" to="$circleLength"  dur="1s" repeatCount="1" fill="freeze" class="progressAnimation"/>
    </circle>

</svg>
EOT;
//  <circle cx="$cx" cy="$cy" r="$radius" stroke="$color" stroke-width="$stroke" fill="none" stroke-dasharray="$circleLength" stroke-dashoffset="$circleLength" transform="rotate(-90, $cx, $cy)" data-circle-length="$circleLength" data-percentage="$percentage" class="progressCircle">
//        <animate attributeName="stroke-dashoffset" from="$circleLength" to="$circleLength" keyTimes="0; 0.5; 1" keySplines=".42 0 .58 1; .42 0 .58 1" dur="1s" begin="0s" fill="freeze" class="progressAnimation"/>
//    </circle>
  return $svg;
}
function getBootstrapColor($color){
  $res = null;
  switch ($color){
    case 'primary':
      $res = 'purple';
      break;
    case 'secondary':
      $res = 'gray';
      break;
    case 'success':
      $res = 'green';
      break;
    case 'danger':
      $res = 'red';
      break;
    case 'warning':
      $res = 'yellow';
      break;
    case 'info':
      $res = 'blue';
      break;
    case 'light':
      $res = 'sky';
      break;
    case 'dark':
      $res = 'sky';
      break;
    default:
      $res = $color;
  }
  return $res;
}
function getLocale(){
  return session()->get('locale', 'ru');
}
function wrapNumbers($str) {
  $pattern = '/(<[^>]*>)|(\d+(:\d+)?)/';

  return preg_replace_callback($pattern, function($matches) {
    // Если это тег, просто возвращаем тег без изменений
    if (isset($matches[1]) && $matches[1]) {
      return $matches[1];
    }

    // Если это число, оборачиваем его в тег
    if (isset($matches[2]) && $matches[2]) {
      return '<span class="cormorantInfant">' . $matches[2] . '</span>';
    }

    return $matches[0];
  }, $str);
}
function formatPhoneNumber($phoneNumber){
  $phoneUtil = PhoneNumberUtil::getInstance();

  try {
    $parsedPhoneNumber = $phoneUtil->parse($phoneNumber);
    $formattedPhoneNumber = $phoneUtil->format($parsedPhoneNumber, PhoneNumberFormat::INTERNATIONAL);
  } catch (\libphonenumber\NumberParseException $e) {
    return $phoneNumber;
  }

  return $formattedPhoneNumber;
}
function getDateFormat($date){
  $format = $date->toTimeString('minutes')=='00:00' || $date->toTimeString('minutes')=='23:59' ? 'd.m.Y' : 'd.m.Y H:i';
  return $date->format($format);
}
function sortByDate($array, $field) {
  usort($array, function($a, $b) use ($field) {
    $dateA = Carbon::parse($a[$field]);
    $dateB = Carbon::parse($b[$field]);
    return $dateA->diffInSeconds($dateB, false);
  });

  return array_reverse($array);
}
function translit($st)
{
  $st = mb_strtolower($st, "utf-8");
  $st = str_replace([
      '?', '!', '.', ',', ':', ';', '*', '(', ')', '{', '}', '[', ']', '%', '#', '№', '@', '$', '^', '-', '+', '/', '\\', '=', '|', '"', '\'',
      'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'з', 'и', 'й', 'к',
      'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х',
      'ъ', 'ы', 'э', ' ', 'ж', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я'
  ], [
      '_', '_', '.', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_',
      'a', 'b', 'v', 'g', 'd', 'e', 'e', 'z', 'i', 'y', 'k',
      'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h',
      'j', 'i', 'e', '_', 'zh', 'ts', 'ch', 'sh', 'shch',
      '', 'yu', 'ya'
  ], $st);
  $st = preg_replace("/[^a-z0-9_.]/", "", $st);
  $st = trim($st, '_');

  $prev_st = '';
  do {
    $prev_st = $st;
    $st = preg_replace("/_[a-z0-9]_/", "_", $st);
  } while ($st != $prev_st);

  $st = preg_replace("/_{2,}/", "_", $st);
  return $st;
}
function getCode($max, $voucher = false) {
  if ($voucher){
    $chars="12345678901234567890123456789012345678901234567890123456789012345678901234567890QAZXSWEDCVFRTGBNHYUJMKLP";
  }else{
    $chars="1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
  }

  $size=StrLen($chars)-1;
  $member_code=null;
  while($max--)
    $member_code.=$chars[rand(0,$size)];
  return $member_code;
}
function isJson($string) {
  json_decode($string);
  return json_last_error() === JSON_ERROR_NONE;
}
function getRealIpAddr() {
  if (!empty($_SERVER['HTTP_CLIENT_IP']))        // Определяем IP
  {
    $ip=$_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))    // Если IP идёт через прокси
  {
    $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  else {
    $ip=$_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}
function getAgent() {
  if (strstr($_SERVER['HTTP_USER_AGENT'], 'YandexBot')) {
    $bot='YandexBot';
  }
  elseif (strstr($_SERVER['HTTP_USER_AGENT'], 'Googlebot')) {
    $bot='Googlebot';}
  else {
    $bot=$_SERVER['HTTP_USER_AGENT'];
  }
  return $bot;
}
function getUrlParams($url){

}
function getRussianMonth($month, $short = true){
  $months = [
      'январь',
      'февраль',
      'март',
      'апрель',
      'май',
      'июнь',
      'июль',
      'август',
      'сентябрь',
      'октябрь',
      'ноябрь',
      'декабрь'
  ];
  $months_short = [
      'янв',
      'фев',
      'мар',
      'апр',
      'май',
      'июн',
      'июл',
      'авг',
      'сен',
      'окт',
      'нояб',
      'дек'
  ];
  if ($short) {
    return $months_short[$month-1];
  }else{
    return $months[$month-1];
  }
}

function getRusDate($time) {
  return date('d', $time).' '.getRussianMonth(date('n', $time)).' '.date('Y', $time);
}
function defineLang($text){
  if (preg_match('/[а-я]/i',$text)) {
    return 'rus';
  } else {
    return 'eng';
  }
}
function clean_search_string( $s ) {
  $s = preg_replace( "/[^\w\d\-_\s]/u", '', $s );

  return $s;
}
function searchForId($id, $field, $array) {
  foreach ($array as $key => $val) {
    if (isset($val[$field])&&$val[$field] === $id) {
      return $key;
    }
  }
  return null;
}
// ['%d файл','%d файла','%d файлов']
function denum($num, $string = array()) {
  $cases = array(2, 0, 1, 1, 1, 2);
  $result = $string[($num%100 > 4 && $num %100 < 20) ? 2 : $cases[min($num%10, 5)]];
  return sprintf($result, $num);
}
function textToList($text, $class = ''){
  $text_arr = explode("\n", $text);
  $res = '';
  foreach($text_arr as $line) {
    $res .= '<li>✔️ '.$line.'</li>';
  }
  return '<ul class="'.$class.'">'.$res.'</ul>';
}
function shorText($text, $long = 80) {
  $short_text = strip_tags($text);
  if (strlen($short_text) > $long) {
    $short_text = substr($short_text, 0, $long);
    $short_text = rtrim($short_text, "!,.-");
    $short_text = rtrim($short_text, "!,.-");
    $short_text = substr($short_text, 0, strrpos($short_text, ' '));
    $short_text = $short_text . '… ';
  }
  return $short_text;
}
function formatPrice($price, $html = false, $r_symb = 'руб.') {
  if($html){
    return '<span class="cormorantInfant">'.number_format($price, 0, '.', ' ').'</span> '.$r_symb;
  }else{
    return number_format($price, 0, '.', ' ').' '.$r_symb;
  }
}
function makeUrltoLink($string) {
  // The Regular Expression filter
  $reg_pattern = "/(((http|https|ftp|ftps)\:\/\/)|(www\.))[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/";

  // make the urls to hyperlinks
  return preg_replace($reg_pattern, '<a href="$0" target="_blank" rel="noopener noreferrer" class="short-link">$0</a>', $string);
}
function pathFromMedia($path, $folder = 'media'){
  $path = mb_stristr($path, '/'.$folder.'/');
  $path_data = explode('/', $path);
  $file_name = $path_data[count($path_data)-1];
  return preg_replace('/'.$file_name.'/', '', mb_stristr($path, '/'.$folder.'/', true));
}
function recursiveRemoveDir($dir) {
  $includes = new FilesystemIterator($dir);
  foreach ($includes as $include) {
    if(is_dir($include) && !is_link($include)) {
      recursiveRemoveDir($include);
    }
    else {
      unlink($include);
    }
  }
  rmdir($dir);
}

function storageToAsset($fullPath){
  // получаем базовый путь к хранилищу
  $storagePath = storage_path('app/public');

// заменяем базовый путь на пустую строку
  $relativePath = str_replace($storagePath, '', $fullPath);

// используем asset() для получения полного URL
  $url = asset('storage' . $relativePath);
  return $url;
}
function filenameFromUrl($url){
  $path = parse_url($url, PHP_URL_PATH);
  $relativePath = ltrim(str_replace('/storage/', '', $path), '/');
  $directory = pathinfo($relativePath, PATHINFO_DIRNAME);
  $extension = pathinfo($relativePath, PATHINFO_EXTENSION);
  $filenameWithoutExtension = pathinfo($relativePath, PATHINFO_FILENAME);
  return $filenameWithoutExtension.'.'.$extension;
}
function urlToStoragePath($url) {
  // Извлекаем путь из URL
  $path = parse_url($url, PHP_URL_PATH);

  // Удаляем префикс '/storage' из пути
  $path = str_replace('/storage', '', $path);

  // Преобразуем путь обратно в полный путь к файлу в директории хранилища
  $storagePath = storage_path('app/public' . $path);

  return $storagePath;
}
function storeImage($file, $size = null)
{
  // создаем дирикторию для картинок
  $time_prefix = time();
  $storage_path = storage_path('app/public/images/compressed/');
  $this_name = $time_prefix . translit(basename($file));
  //Storage::makeDirectory($directory, 0755, true, true);
  if (!file_exists($storage_path)) {
    mkdir($storage_path, 0777, true);
  }
  $img = Img::make($file)->orientate();

  $width = $img->width();
  $height = $img->height();
  if(!$size){
    $size = max($width, $height);
  }

  $path = $storage_path . $this_name;
  $size_w = $width >= $height ? $size : null;
  $size_h = $height >= $width ? $size : null;
  $img->resize($size_w, $size_h, function ($constraint) {
    $constraint->aspectRatio();
    $constraint->upsize();
  })->save($path, 80);

  return storageToAsset($path);
}

function findModel($notifiableType, $notifiableId)
{
  $model = app($notifiableType);
  $notifiable = $model::find($notifiableId);

  return $notifiable;
}

function getRatingStars($rating) {
  $filledStar = '
    <svg class="md:w-6 w-[18px]" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 0L14.6942 8.2918H23.4127L16.3593 13.4164L19.0534 21.7082L12 16.5836L4.94658 21.7082L7.64074 13.4164L0.587322 8.2918H9.30583L12 0Z" fill="#2C2E35"/>
    </svg>';

  $unfilledStar = '
    <svg class="md:w-6 w-[18px]" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path opacity="0.32" d="M12 0L14.6942 8.2918H23.4127L16.3593 13.4164L19.0534 21.7082L12 16.5836L4.94658 21.7082L7.64074 13.4164L0.587322 8.2918H9.30583L12 0Z" fill="#2C2E35"/>
    </svg>';

  $output = '';

  for ($i = 0; $i < 5; $i++) {
    if ($i < floor($rating)) {
      $output .= $filledStar;
    } else {
      $output .= $unfilledStar;
    }
  }

  return $output;
}

function getShortName($first_name = null, $last_name = null){
  $last_name = $last_name ? mb_substr($last_name, 0, 1, "UTF-8").'.' : null;
  return $first_name.' '.$last_name;
}
function deleteDirectory($dir) {
  // Получаем полный путь до папки storage
  $storagePath = storage_path();

  // Проверяем, начинается ли путь директории с пути до папки storage
  if (strpos($dir, $storagePath) !== 0) {
    throw new \Exception("You can delete only inside the storage directory.");
  }

  if (!file_exists($dir)) {
    return true;
  }

  if (!is_dir($dir)) {
    return unlink($dir);
  }

  foreach (scandir($dir) as $item) {
    if ($item == '.' || $item == '..') {
      continue;
    }

    if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
      return false;
    }
  }

  return rmdir($dir);
}
function getStatusBadge($status_code = null, $class = null)
{
  if(!$status_code){
    return '<span class="badge-gray whitespace-nowrap '.$class.'">В обработке</span>';
  }
  $status = Status::where('key', '=', $status_code)->first();
  return '<span class="badge-' . getBootstrapColor($status->color) . ' whitespace-nowrap '.$class.'">' . $status->name . '</span>';
}
function addToZip(ZipArchive $zip, $dir, $baseDir = '') {
  $files = scandir($dir);

  foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
      $filePath = $dir . '/' . $file;
      $inZipPath = $baseDir . $file;

      if (is_dir($filePath)) {
        $zip->addEmptyDir($inZipPath);
        addToZip($zip, $filePath, $inZipPath . '/');
      } else {
        $zip->addFile($filePath, $inZipPath);
      }
    }
  }
}

function mergeItemsById($cart, $field = 'id') {
  $result = [];

  foreach ($cart as $item) {
    // Проверяем, существует ли уже такой ID в результирующем массиве.
    if (isset($result[$item[$field]])) {
      // Если да, увеличиваем количество на значение qty текущего элемента.
      $result[$item[$field]]['qty'] += $item['qty'];
    } else {
      // Если нет, добавляем этот элемент в результирующий массив.
      $result[$item[$field]] = $item;
    }
  }
  ksort($result);
  return array_values($result); // Возвращаем результирующий массив без ключей для сохранения структуры массива.
}
function sortItemsByName($cart, $field = 'name') {
  // Функция usort для сортировки массива с пользовательской функцией сравнения.
  usort($cart, function($a, $b) use ($field) {
    // Сравнение имен товаров (строк) для упорядочивания по возрастанию.
    if(is_numeric($a[$field])&&is_numeric($b[$field])){
      return $a[$field] <=> $b[$field];
    }else{
      return strcmp($a[$field], $b[$field]);
    }

  });

  return $cart; // Возвращаем отсортированный массив.
}
function cleanSpaces($string) {
  // Заменяем все варианты переносов строк на пробелы
  $string = str_replace(["\r\n", "\r", "\n"], ' ', $string);

  // Заменяем HTML-сущность &nbsp; на обычный пробел
  $string = str_replace('&nbsp;', ' ', $string);

  // Заменяем HTML-код неразрывного пробела на обычный пробел
  $string = str_replace("\xc2\xa0", ' ', $string);

  // Убираем множественные пробелы
  $string = preg_replace('/\s+/', ' ', $string);

  // Убираем пробелы в начале и конце строки
  return trim($string);
}
class SafeObject {
  private $attributes = [];

  public function __construct(array $attributes) {
    $this->attributes = $attributes;
  }

  public function __get($name) {
    return $this->attributes[$name] ?? null;
  }

  public function __isset($name) {
    return isset($this->attributes[$name]);
  }
}
function hasVisibleItem(array $array) {
  foreach ($array as $item) {
    // Проверяем, отсутствует ли ключ 'hidden' или он равен false
    if (!isset($item['hidden']) || $item['hidden'] === false) {
      return true;
    }
  }
  return false;
}

function searchInMultidimensionalArray($array, $key, $value) {
  foreach ($array as $subarray) {
    if (isset($subarray[$key]) && $subarray[$key] == $value) {
      return $subarray;
    }
  }
  return null; // если элемент не найден
}

function getOrderSliderJson($products, $items){
  $order = [];
  foreach($items as $product_id){
    $product = $products->where('id', $product_id)->first();
    if(!$product){
      continue;
    }
    $order[] = [
        'id' => $product_id,
        'text' => $product->name ?? 'Неизвестынй элемент'
    ];
  }
  return $order;
}
function replaceNullWithFalse($model)
{
  $attributes = $model->getAttributes();

  foreach ($attributes as $key => $value) {
    if ($value === null) {
      $model->setAttribute($key, false);
    }
  }

  return $model;
}

function getStockStatus($product) {
  $quantity = $product->quantity;
  if($product->promoQuantity && $product->promoStatus){
    $quantity = $product->promoQuantity;
  }
  $maxQuantity = 1500;
  $minVisiblePercent = 5;
  $maxVisiblePercent = 100;

  $result = [
      'color' => '',
      'text' => '',
      'percent' => $maxVisiblePercent
  ];

  // Расчет визуального процента
  if ($quantity <= $maxQuantity) {
    $result['percent'] = $minVisiblePercent +
        (($maxVisiblePercent - $minVisiblePercent) * $quantity / $maxQuantity);
  }



  // Color logic
  if ($quantity > 1500) {
    $result['color'] = '#2C4B3A'; // Базовый зеленый
  } elseif ($quantity > 1000) {
    $result['color'] = '#2C4B3A'; // Чуть более светлый
  } elseif ($quantity > 500) {
    $result['color'] = '#4E6255'; // Еще светлее
  } elseif ($quantity > 200) {
    $result['color'] = '#6C7D73'; // Самый светлый зеленый BFDDCC
  } elseif ($quantity > 50) {
    $result['color'] = '#E07A84'; // Светлый красный E3B1B1
  } elseif ($quantity > 40) {
    $result['color'] = '#C14D58'; // Светлый красный
  } elseif ($quantity > 30) {
    $result['color'] = '#993047'; // Светлый красный
  } else {
    $result['color'] = '#810E19'; // Базовый красный
  }

  // Text logic
  if ($quantity > 1500) {
    $result['text'] = 'более 1500';
  } elseif ($quantity > 1000) {
    $result['text'] = 'более 1000';
  } elseif ($quantity > 500) {
    $result['text'] = 'менее 1000';
  } elseif ($quantity > 200) {
    $result['text'] = 'менее 500';
  } elseif ($quantity > 50) {
    $result['text'] = 'менее 200';
  } elseif ($quantity > 40) {
    $result['text'] = 'менее 50';
  } elseif ($quantity > 30) {
    $result['text'] = 'менее 40';
  } elseif ($quantity > 20) {
    $result['text'] = 'менее 30';
  } elseif ($quantity > 10) {
    $result['text'] = 'менее 20';
  } elseif ($quantity > 5) {
    $result['text'] = 'менее 10';
  } else {
    $result['text'] = 'менее 5';
  }

  return $result;
}
function getRandomNumber($number = null) {
  // Определяем границы основного диапазона
  $minRange = 123;
  $maxRange = 376;

  // Если число не передано, просто возвращаем случайное число из диапазона
  if ($number === null || $number < $minRange || $number > $maxRange) {
    return mt_rand($minRange, $maxRange);
  }

  do {
    // Генерируем случайное отклонение от 18 до 21
    $deviation = mt_rand(18, 21);

    // Случайно выбираем, прибавить или отнять отклонение
    $sign = (mt_rand(0, 1) == 1) ? 1 : -1;
    $result = $number + ($deviation * $sign);

    // Проверяем и корректируем результат, если он вышел за пределы диапазона
    if ($result < $minRange) {
      $result = $minRange;
    } elseif ($result > $maxRange) {
      $result = $maxRange;
    }

    // Повторяем, если результат совпал с входным числом
  } while ($result == $number);

  return $result;
}
function sortArrayByDate(array $data): array {
  uksort($data, function($a, $b) {
    $dateA = DateTime::createFromFormat('d.m.y H:i', $a);
    $dateB = DateTime::createFromFormat('d.m.y H:i', $b);
    return $dateA <=> $dateB;
  });
  return $data;
}
function extractCode(string $input): string
{
  // Проверка, является ли строка URL
  if (filter_var($input, FILTER_VALIDATE_URL)) {
    // Удаление query string и хвостовых слэшей
    $path = parse_url($input, PHP_URL_PATH);
    $path = rtrim($path, '/');

    // Разбиение пути и получение последнего сегмента
    $segments = explode('/', $path);
    return end($segments);
  }

  // Если это не URL — вернуть исходную строку
  return $input;
}
