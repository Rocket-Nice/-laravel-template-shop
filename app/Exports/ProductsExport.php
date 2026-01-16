<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use SafeObject;

class ProductsExport implements  FromQuery, WithChunkReading, WithHeadings, WithMapping
{
  use Exportable;

  public function chunkSize(): int
  {
    return 100;
  }

  private $request;

  public function __construct($request){
    $this->request = $request;
  }
  public function query()
  {
    $select = [
        'id',
        'name',
        'sku',
        'product_sku_id',
        'weight',
        'volume',
        'old_price',
        'price',
        'quantity',
        'status',
        'hidden',
        'order',
        'keywords',
        'slug',
        'created_at',
        'is_producing',
        'in_stock_wb',
        'in_stock_ozon',
        'comment'
    ];
    return Product::query()
        ->select($select)
        ->with('category')
        ->where('type_id', 1)
        ->filtered(new SafeObject($this->request));
  }

  public function map($product): array
  {
    return [
        $product->id, // 'ID',
        $product->name, // 'Имя',
        $product->product_sku?->name, // 'Артикул',
        $product->category?->title, // 'Категория',
        $product->weight, // 'Вес г.',
        $product->volume, // 'Объем',
        $product->price, // 'Цена',
        $product->old_price, // 'Цена',
        $product->quantity, // 'Количество',
        $product->status ? 'Да' : 'Нет', // 'Статус наличия',
        $product->hidden ? 'Да' : 'Нет', // 'Скрытй товар',
        $product->order, // 'Порядок',
        $product->keywords, // 'Ключевые слова',
        route('product.index', $product->slug), // 'Ссылка',
        $product->is_producing === null ? 'Не указано' : ($product->is_producing ? 'Да' : 'Нет'), // 'Производится',
        $product->in_stock_wb === null ? 'Не указано' : (\App\Models\Product::MARKETPLACE_STATUS[$product->in_stock_wb] ?? $product->in_stock_wb), // 'Наличие на WB',
        $product->in_stock_ozon === null ? 'Не указано' : (\App\Models\Product::MARKETPLACE_STATUS[$product->in_stock_ozon] ?? $product->in_stock_ozon), // 'Наличие на ОЗОН',
        $product->comment, // 'Комментарий',
        $product->created_at,
    ];
  }

  public function headings(): array
  {
    return [
        'ID',
        'Имя',
        'Артикул',
        'Категория',
        'Вес г.',
        'Объем',
        'Цена',
        'Старая цена',
        'Количество',
        'Статус наличия',
        'Скрытй товар',
        'Порядок',
        'Ключевые слова',
        'Ссылка',
        'Производится',
        'Наличие на WB',
        'Наличие на ОЗОН',
        'Комментарий',
        'Дата создания',
    ];
  }
}
