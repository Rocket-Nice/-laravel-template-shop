<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductSkuImport implements ToCollection
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
        'product_name' => null,
        'sku' => null,
        'product_id' => null,
    ];

    // Определяем индексы нужных колонок по названию
    foreach ($headerRow as $index => $cell) {
      $value = trim(mb_strtolower($cell));
      if (str_contains($value, 'ссылка')) {
        $columns['product_name'] = $index;
      } elseif (str_contains($value, 'артикул новый')) {
        $columns['sku'] = $index;
      } elseif (str_contains($value, 'id с сайта')) {
        $columns['product_id'] = $index;
      }
    }
    if (!isset($columns['product_name'])) {
      throw new \Exception('Колонка с именем товара не найдена.');
    }
    if (!isset($columns['sku'])) {
      throw new \Exception('Колонка с артикулом не найдена.');
    }
    if (!isset($columns['product_id'])) {
      throw new \Exception('Колонка с id товара не найдена.');
    }

    // Обработка оставшихся строк
    foreach ($collection->slice(1) as $row) {
      if(!isset($row[$columns['sku']])||!isset($row[$columns['product_id']])){
        continue;
      }
      $sku_name = trim($row[$columns['sku']]);
      $sku = null;
      if($sku_name){
        $sku = ProductSku::query()->where('name', trim(mb_strtolower($sku_name)))->first();
        if(!$sku){
          $sku = ProductSku::create([
              'name' => mb_strtolower(trim($sku_name))
          ]);
        }
      }
      $product_id = $row[$columns['product_id']];
      if(!is_numeric($product_id)){
        continue;
      }
      $product = Product::query()->find($product_id);
      if($product){
        $product->update([
            'product_sku_id' => $sku->id,
        ]);
      }
    }
  }
}
