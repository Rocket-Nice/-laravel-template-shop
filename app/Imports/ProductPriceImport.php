<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductPriceImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
  public function collection(\Illuminate\Support\Collection $collection)
  {
    if ($collection->isEmpty()) {
      return;
    }
    if ($collection->count() < 1) {
      throw new \Exception('Файл не содержит достаточно строк для заголовков.');
    }

    $headerRow = $collection[0];

    $columns = [
        'id' => null,
        'old_price' => null,
        'price' => null,
    ];

    // Определяем индексы нужных колонок по названию
    foreach ($headerRow as $index => $cell) {
      $value = trim(mb_strtolower($cell));
      if (str_contains($value, 'id')) {
        $columns['id'] = $index;
      } elseif (str_contains($value, 'старая цена')) {
        $columns['old_price'] = $index;
      } elseif (str_contains($value, 'новая цена')) {
        $columns['price'] = $index;
      }
    }
    if (!isset($columns['id'])) {
      throw new \Exception('Колонка с id товара не найдена.');
    }
    if (!isset($columns['price'])) {
      throw new \Exception('Колонка с ценой товара не найдена.');
    }

    // Обработка оставшихся строк
    foreach ($collection->slice(1) as $row) {
      if(!isset($row[$columns['id']])){
        continue;
      }
      $product_id = $row[$columns['id']];
      if(!is_numeric($product_id)){
        continue;
      }
      $product = Product::query()->find($product_id);
      if($product){
        $product->update([
            'old_price' => $row[$columns['old_price']],
            'price' => $row[$columns['price']] ?? 0,
        ]);
      }
    }
  }
}
