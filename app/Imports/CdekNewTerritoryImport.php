<?php

namespace App\Imports;

use App\Models\CdekCity;
use App\Models\CdekNewTerritory;
use App\Models\CdekPvz;
use App\Models\CdekRegion;
use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CdekNewTerritoryImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
      if ($collection->isEmpty()) {
        return;
      }
      if ($collection->count() < 1) {
        throw new \Exception('Файл не содержит достаточно строк для заголовков.');
      }

      $headerRow = $collection[0];

      $columns = [
          'address' => null,
          'code' => null,
          'phone' => null,
          'email' => null,
      ];

      // Определяем индексы нужных колонок по названию
      foreach ($headerRow as $index => $cell) {
        $value = trim(mb_strtolower($cell));
        if (str_contains($value, 'адрес')) {
          $columns['address'] = $index;
        } elseif (str_contains($value, 'код')) {
          $columns['code'] = $index;
        } elseif (str_contains($value, 'телефон')) {
          $columns['phone'] = $index;
        } elseif (str_contains($value, 'почта')) {
          $columns['email'] = $index;
        }
      }
      if (!isset($columns['code'])) {
        throw new \Exception('Колонка с кодом товара не найдена.');
      }
      function checkColumns(array $data): bool
      {
        foreach ($data as $value) {
          if ($value !== null) {
            return false;
          }
        }
        return true;
      }

      if (checkColumns($columns)) {
        throw new \Exception('Не найдено данных для импорта');
      }

      // Обработка оставшихся строк
      foreach ($collection->slice(1) as $row) {
        $address = $row[$columns['address']];
        $code = $row[$columns['code']];
        $phone = $row[$columns['phone']];
        $email = $row[$columns['email']];
        $cdekTerritory = CdekNewTerritory::query()->where('code', $code)->first();
        $cdekPvz = CdekPvz::query()->where('code', $code)->first();
        $params = [
            'address' => $address,
            'code' => $code,
            'phone' => $phone,
            'email' => $email,
            'pvz_id' => $cdekPvz?->id
        ];
        if($cdekTerritory){
          $cdekTerritory->update($params);
        }else{
          CdekNewTerritory::create($params);
        }
        if($cdekPvz){
          $region = CdekRegion::query()->where('region_code', CdekRegion::NEW_TERRITORY_REGION_CODE)->first();
          if(!$region){
            $region = CdekRegion::create([
                'region' => CdekRegion::NEW_TERRITORY_REGION_NAME,
                'region_code' => CdekRegion::NEW_TERRITORY_REGION_CODE,
                'country' => 'Россия',
                'country_code' => 'RU'
            ]);
          }else{
            $region->update([
                'region' => CdekRegion::NEW_TERRITORY_REGION_NAME
            ]);
          }
          $city = CdekCity::query()->where('code', CdekCity::NEW_TERRITORY_CITY_CODE)->first();
          if(!$city){
            $city = CdekCity::create([
                "code" => CdekCity::NEW_TERRITORY_CITY_CODE,
                "city" => CdekCity::NEW_TERRITORY_CITY_NAME,
                "country_code" => 'RU',
                "region" => $region->region,
                "region_code" => $region->region_code,
                "region_id" => $region->id,
            ]);
          }else{
            $city->update([
                "code" => CdekCity::NEW_TERRITORY_CITY_CODE
            ]);
          }
          $cdekPvz->update([
              'region_code' => $region->region_code,
              'region' => $region->region,
              'region_id' => $region->id,
              'city_code' => $city->code,
              'city' => $city->city,
              'city_id' => $city->id,
              'longitude' => null,
              'latitude' => null,
              'address' => $address,
              'address_full' => $address,
              'pvz_data' => json_encode([]),
              'is_active' => true,
              'is_updating' => false,
          ]);
        }
      }
    }
}
