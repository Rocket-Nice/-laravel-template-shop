<?php

namespace App\Http\Controllers\Shipping;

use AntistressStore\CdekSDK2\Entity\Requests\DeliveryPoints;
use AntistressStore\CdekSDK2\Entity\Requests\Item;
use AntistressStore\CdekSDK2\Entity\Requests\Location;
use AntistressStore\CdekSDK2\Entity\Requests\Package;
use AntistressStore\CdekSDK2\Entity\Requests\Tariff;
use AntistressStore\CdekSDK2\Exceptions\CdekV2RequestException;
use App\Http\Controllers\Controller;
use App\Jobs\checkCdekCourierCitiesJob;
use App\Jobs\getCdekTicketsJob;
use App\Models\CdekCity;
use App\Models\CdekCourierCity;
use App\Models\CdekNewTerritory;
use App\Models\CdekPvz;
use App\Models\CdekRegion;
use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\Region;
use App\Models\ShippingLog;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use App\Jobs\UpdateCdekCitiesJob;
use App\Jobs\UpdateCdekCourierCitiesJob;
use App\Jobs\UpdateCdekPvzJob;
use App\Models\CdekOrder;
use App\Services\MailSender;
use App\Services\TelegramSender;
use App\Services\tfpdf\tFPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorJPG;

class CdekController extends Controller
{
    public $cdek;

    public function __construct($old = false)
    {
        // $cdek_client = new \AntistressStore\CdekSDK2\CdekClientV2('17Vx7EJsAPp1BE7VrdRiUliyCemM8Aek', '7s8ygF9KJk1O4BUEa8QswY60wNhPa6K2'); // яковлева
        //    if($old){
        //      // старый ключ
        //      $cdek_client = new \AntistressStore\CdekSDK2\CdekClientV2('vbGhFm3NsOLP4MK8S3pkyu6IrTxX7EIy', 'aErXNRjTUSusdolnmu0GeaKKRFoSPEBp', 60);
        //    }else{
        //      // новый ключ
        //      $cdek_client = new \AntistressStore\CdekSDK2\CdekClientV2('BUyj6qyUNPlZ5Xp7nAsTn91RGLNi4fLr', 'tqnif6MnzfhRx3Ce4XxY6RqU06lGpKCL', 60);
        //    }
        $cdek_client = new \AntistressStore\CdekSDK2\CdekClientV2('BUyj6qyUNPlZ5Xp7nAsTn91RGLNi4fLr', 'tqnif6MnzfhRx3Ce4XxY6RqU06lGpKCL', 60);
        // овчинникова
        $cdekSdkAuthSaver = function (array $memory) {
            session($memory); // сохраняем в сессии возвращенный массив с данными авторизации
            return true;
        };
        $this->cdek = $cdek_client;
        $this->cdek->setMemory(session('cdekAuth'), $cdekSdkAuthSaver);
    }

    // расчет стоимости доставки
    public function calc($city)
    {
        $package = (new Package())
            ->setWidth(30)
            ->setHeight(20)
            ->setLength(16)
            ->setWeight(1000);
        $tariff = (new Tariff())
            ->setTariffCode(136) // Указывает код тарифа для расчета
            ->setCityCodes(426, $city) // Экспресс-метод установки кодов отправителя и получателя// Экспресс-метод установки кодов отправителя и получателя
            ->setPackageWeight(1000)
            ->setPackages($package);
        $tariff_express = (new Tariff())
            ->setTariffCode(483) // Указывает код тарифа для расчета
            ->setCityCodes(426, $city) // Экспресс-метод установки кодов отправителя и получателя// Экспресс-метод установки кодов отправителя и получателя
            ->setPackageWeight(1000)
            ->setPackages($package);
        $response = [];
        try {
            $tariff_response = $this->cdek->calculateTariff($tariff);
            $response['shippingPrice'] = $tariff_response->getTotalSum();
        } catch (CdekV2RequestException $cdekV2RequestException) {
            $err_message = $cdekV2RequestException->getMessage();
            return ['error' => $err_message];
        }
        try {
            $tariff_express_response = $this->cdek->calculateTariff($tariff_express);
            $response['shippingPriceExpress'] = $tariff_express_response->getTotalSum();
        } catch (CdekV2RequestException $cdekV2RequestException) {
            $err_message = $cdekV2RequestException->getMessage();
            $response['shippingPriceExpress'] = null;
        }
        return $response;
    }

    public function calcCourier($city)
    {
        $package = (new Package())
            ->setWidth(30)
            ->setHeight(20)
            ->setLength(16)
            ->setWeight(1000);
        $tariff = (new Tariff())
            ->setTariffCode(137) // Указывает код тарифа для расчета
            ->setCityCodes(426, $city) // Экспресс-метод установки кодов отправителя и получателя// Экспресс-метод установки кодов отправителя и получателя
            ->setPackageWeight(1000)
            ->setPackages($package);
        $tariff_express = (new Tariff())
            ->setTariffCode(482) // Указывает код тарифа для расчета
            ->setCityCodes(426, $city) // Экспресс-метод установки кодов отправителя и получателя// Экспресс-метод установки кодов отправителя и получателя
            ->setPackageWeight(1000)
            ->setPackages($package);
        $response = [];
        try {
            $tariff_response = $this->cdek->calculateTariff($tariff);
            $response['shippingPrice'] = $tariff_response->getTotalSum();
        } catch (CdekV2RequestException $cdekV2RequestException) {
            $err_message = $cdekV2RequestException->getMessage();
            return ['error' => $err_message];
        }
        try {
            $tariff_express_response = $this->cdek->calculateTariff($tariff_express);
            $response['shippingPriceExpress'] = $tariff_express_response->getTotalSum();
        } catch (CdekV2RequestException $cdekV2RequestException) {
            $err_message = $cdekV2RequestException->getMessage();
            $response['shippingPriceExpress'] = null;
        }
        return $response;
    }

    public function checkStatus(Order $order)
    {
        $data_shipping = $order->data_shipping;
        if (isset($data_shipping['cdek']['uuid']) || isset($data_shipping['cdek_courier']['uuid'])) {
            $shipping_code = $data_shipping['shipping-code'];
            $check_cdek = $this->getOrder($data_shipping[$shipping_code]['uuid']);

            if (false) {
                dd($check_cdek);
            }
            if (is_array($check_cdek) && isset($check_cdek['error']) && mb_strpos($check_cdek['error'], 'Entity is forbidden') === false) {
                if (isset($data_shipping['ticket']) && (isset($data_shipping['cdek']) || isset($data_shipping['cdek_courier']))) {
                    return true;
                }
                if (isset($data_shipping['cdek'])) {
                    $data_shipping['old_cdek'] = $data_shipping['cdek'];
                    unset($data_shipping['cdek']);
                }
                if (isset($data_shipping['cdek_courier'])) {
                    $data_shipping['old_cdek_courier'] = $data_shipping['cdek_courier'];
                    unset($data_shipping['cdek_courier']);
                }
                $order->update([
                    'data_shipping' => $data_shipping
                ]);
                $order->setStatus('has_error');
                ShippingLog::create([
                    'code' => 'cdek',
                    'title' => 'Ошибка в заказе ' . $order->id,
                    'text' => $check_cdek['error'],
                ]);

                return $check_cdek;
            } elseif (!is_array($check_cdek)) {
                // обновляем статус заказа
                $data_shipping[$shipping_code]['invoice_number'] = $check_cdek->getCdekNumber();
                $order->update([
                    'data_shipping' => $data_shipping
                ]);
                if (isset($check_cdek->getStatuses()[0])) {
                    $status_code = $check_cdek->getStatuses()[0]->getCode();
                    $db_status = Status::where(DB::raw('lower(`key`)'), '=', mb_strtolower('cdek_' . $status_code))->first();
                    if (!$db_status) {
                        Log::debug('cdek_' . $status_code);
                        Status::create([
                            'key' => 'cdek_' . $status_code,
                            'name' => 'Сдэк: ' . $status_code,
                            'color' => 'warning'
                        ]);
                        Status::flushQueryCache();
                    }
                    $status_code = 'cdek_' . $status_code;
                    // сохраняем статус
                    $user = $order->user;
                    if (
                        $status_code != $order->status &&
                        (
                            mb_strtolower($status_code) == 'cdek_created' || mb_strtolower($status_code) == 'cdek_1'
                        ) &&
                        !$order->status_history()->whereIn(DB::raw('lower(status)'), ['cdek_created', 'cdek_1'])->exists() &&
                        isset($order->data['form']['email'])
                        && $user->is_subscribed_to_marketing
                    ) {
                        (new MailSender($order->data['form']['email']))->trakingMessage($order);
                        $tgChats = $user->tgChats;
                        foreach ($tgChats as $tgChat) {
                            (new TelegramSender($tgChat))->trakingMessage($order);
                        }
                    }

                    $order->setStatus($status_code);
                }

                return $check_cdek;
            }
        }
        return false;
    }

    // api получение статусов от сдек
    public function status(Request $request)
    {
        $attributes = $request->toArray();
        // Log::debug('Заказ сдек не найден '.print_r($attributes, true));
        $order = Order::where('data_shipping->cdek->uuid', $attributes['uuid'])->first();
        if (!$order) {
            $order = Order::where('data_shipping->cdek_courier->uuid', $attributes['uuid'])->first();
        }

        if ($order) {
            $order_data = $order->data;
            $order_data_shipping = $order->data_shipping;
            $order_data_shipping[$order_data_shipping['shipping-code']]['invoice_number'] = $attributes['attributes']['cdek_number'];
            $status_key = 'cdek_' . $attributes['attributes']['status_code'];
            if (isset($attributes['attributes']['code'])) {
                $db_status = Status::where('key', 'cdek_' . $attributes['attributes']['code'])->first();
                if (!$db_status) {
                    Status::create([
                        'key' => 'cdek_' . $attributes['attributes']['code'],
                        'name' => 'Сдэк: ' . $attributes['attributes']['code'],
                        'color' => 'warning'
                    ]);
                }
                $status_key = 'cdek_' . $attributes['attributes']['code'];
            }
            // сохраняем статус
            $order->setStatus($status_key);
            $user = $order->user;
            $tgChats = $user->tgChats;
            //      foreach($tgChats as $tgChat){
            //        (new TelegramSender($tgChat))->customMessage("Статус вашего заказа #$order->getOrderNumber() изменен на ".$attributes['attributes']['code']);
            //      }
            $order->update([
                'data_shipping' => $order_data_shipping
            ]);
            if ($user->is_subscribed_to_marketing) {
                if (mb_strtolower($status_key) == 'cdek_created' || mb_strtolower($status_key) == 'cdek_1') {
                    (new MailSender($order_data['form']['email']))->trakingMessage($order);
                    foreach ($tgChats as $tgChat) {
                        (new TelegramSender($tgChat))->trakingMessage($order);
                    }
                }
            }
        } else {
            // Log::debug('Заказ сдек не найден '.print_r($attributes, true));
        }
        return true;
    }

    public function updateRegions($page = 0)
    {
        try {
            $r_request = (new Location())->setCountryCodes('RU,KZ,BY,AM,KG')->setPage($page)->setSize(300);;
            $regions = $this->cdek->getRegions($r_request);
        } catch (CdekV2RequestException $ex) {
            $regions = null;
        }
        if ($regions) {
            foreach ($regions as $region) {
                $region_db = CdekRegion::where('region_code', $region->getRegionCode())->first();

                if (!$region_db) {
                    $country = Country::where('name', $region->getCountry())->first();
                    $country_id = null;
                    $region_id = null;
                    if ($country) {
                        $country_id = $country->id;
                        $main_region = Region::query()->where('name', $region->getRegion())->first();
                        if (!$main_region) {
                            $main_region = Region::create([
                                'name' => $region->getRegion(),
                                'country_id' => $country->id,
                            ]);
                            Region::flushQueryCache();
                        }
                        $region_id = $main_region->id;
                    }


                    CdekRegion::create([
                        'region' => $region->getRegion(),
                        'region_code' => $region->getRegionCode(),
                        'country' => $region->getCountry(),
                        'country_code' => $region->getCountryCode(),
                        'lm_country_id' => $country_id,
                        'lm_region_id' => $region_id
                    ]);
                }
            }
            // UpdateCdekRegionsJob::dispatch($page+1)->onQueue('cdek_regions');
        }
    }

    public function updateCities($page = 0)
    {
        try {
            $cdek_request = (new Location())->setSize(200)->setPage($page);
            //dd($pvzs,$cdek_request);
            $cities = $this->cdek->getCities($cdek_request);
        } catch (CdekV2RequestException $ex) {
            Log::error($ex->getMessage());
            $cities = [];
        }
        if (count($cities)) {
            foreach ($cities as $city) {
                $pvz = CdekPvz::where('city_code', $city->getCode())->count();
                if (!$pvz) {
                    continue;
                }


                $city_db = CdekCity::where('code', $city->getCode())->first();

                $region_obj = CdekRegion::select('id', 'region_code')->where('region_code', $city->getRegionCode())->first();

                $lm_country_id = null;
                $lm_region_id = null;
                $lm_city_id = null;
                if ($region_obj) {
                    $region = $region_obj->lm_region;
                    if ($region) {
                        $country = $region->country;
                        if ($country) {
                            $db_city = City::query()->where('name', $city->city)->where('region_id', $region->id)->first();
                            if (!$db_city) {
                                $db_city = City::create([
                                    'name' => $city->city,
                                    'country_id' => $country->id,
                                    'region_id' => $region->id,
                                ]);
                            }
                            $lm_country_id = $country->id;
                            $lm_region_id = $region->id;
                            $lm_city_id = $db_city->id;
                        }
                    }
                }

                if (!$city_db) {
                    CdekCity::create([
                        "code" => $city->getCode(),
                        "city" => $city->getCity(),
                        "fias_guid" => $city->getFiasGuid(),
                        "country_code" => $city->getCountryCode(),
                        "region" => $city->getRegion(),
                        "region_code" => $city->getRegionCode(),
                        "region_id" => $region_obj->id ?? null,
                        "sub_region" => $city->getSubRegion(),
                        "longitude" => $city->getLongitude(),
                        "latitude" => $city->getLatitude(),
                        "time_zone" => $city->getTimeZone(),
                        "lm_country_id" => $lm_country_id,
                        "lm_region_id" => $lm_region_id,
                        "lm_city_id" => $lm_city_id,
                    ]);
                } else {
                    $city_db->update([
                        "code" => $city->getCode(),
                        "city" => $city->getCity(),
                        "fias_guid" => $city->getFiasGuid(),
                        "country_code" => $city->getCountryCode(),
                        "region" => $city->getRegion(),
                        "region_code" => $city->getRegionCode(),
                        "region_id" => $region_obj->id ?? null,
                        "sub_region" => $city->getSubRegion(),
                        "longitude" => $city->getLongitude(),
                        "latitude" => $city->getLatitude(),
                        "time_zone" => $city->getTimeZone(),
                        "lm_country_id" => $lm_country_id,
                        "lm_region_id" => $lm_region_id,
                        "lm_city_id" => $lm_city_id,
                    ]);
                }
            }
            UpdateCdekCitiesJob::dispatch($page + 1)->onQueue('cdek_cities');
        }
    }

    public function updateCourierCities($page = 0)
    {
        try {
            $cdek_request = (new Location())->setSize(200)->setPage($page);
            //dd($pvzs,$cdek_request);
            $cities = $this->cdek->getCities($cdek_request);
        } catch (CdekV2RequestException $ex) {
            Log::error($ex->getMessage());
            $cities = [];
        }
        if (count($cities)) {
            foreach ($cities as $city) {
                //        $pvz = CdekPvz::where('city_code', $city->getCode())->count();
                //        if (!$pvz){
                //          continue;
                //        }
                if (!in_array($city->getCountryCode(), ['RU', 'KZ', 'BY', 'AM', 'KG'])) {
                    continue;
                }
                $city_db = CdekCourierCity::where('code', $city->getCode())->first();

                $region_obj = CdekRegion::select('id', 'region_code')->where('region_code', $city->getRegionCode())->first();

                $lm_country_id = null;
                $lm_region_id = null;
                $lm_city_id = null;
                if ($region_obj) {
                    $region = $region_obj->lm_region;
                    if ($region) {
                        $country = $region->country;
                        if ($country) {
                            $db_city = City::query()->where('name', $city->city)->where('region_id', $region->id)->first();
                            if (!$db_city) {
                                $db_city = City::create([
                                    'name' => $city->city,
                                    'country_id' => $country->id,
                                    'region_id' => $region->id,
                                ]);
                                City::flushQueryCache();
                            }
                            $lm_country_id = $country->id;
                            $lm_region_id = $region->id;
                            $lm_city_id = $db_city->id;
                        }
                    }
                }


                if (!$city_db) {
                    CdekCourierCity::create([
                        "code" => $city->getCode(),
                        "city" => $city->getCity(),
                        "fias_guid" => $city->getFiasGuid(),
                        "country_code" => $city->getCountryCode(),
                        "region" => $city->getRegion(),
                        "region_code" => $city->getRegionCode(),
                        "region_id" => $region_obj->id ?? null,
                        "sub_region" => $city->getSubRegion(),
                        "longitude" => $city->getLongitude(),
                        "latitude" => $city->getLatitude(),
                        "time_zone" => $city->getTimeZone(),
                        'active' => false,
                        "lm_country_id" => $lm_country_id,
                        "lm_region_id" => $lm_region_id,
                        "lm_city_id" => $lm_city_id,
                    ]);
                } else {
                    $city_db->update([
                        "code" => $city->getCode(),
                        "city" => $city->getCity(),
                        "fias_guid" => $city->getFiasGuid(),
                        "country_code" => $city->getCountryCode(),
                        "region" => $city->getRegion(),
                        "region_code" => $city->getRegionCode(),
                        "region_id" => $region_obj->id ?? null,
                        "sub_region" => $city->getSubRegion(),
                        "longitude" => $city->getLongitude(),
                        "latitude" => $city->getLatitude(),
                        "time_zone" => $city->getTimeZone(),
                        "lm_country_id" => $lm_country_id,
                        "lm_region_id" => $lm_region_id,
                        "lm_city_id" => $lm_city_id,
                    ]);
                }
            }
            UpdateCdekCourierCitiesJob::dispatch($page + 1)->onQueue('cdek_courier_cities');
        }
    }

    public function checkCdekCourierCities($page = 1)
    {
        $itemsPerPage = 1000; // Number of items per page
        $selectedPage = $page < 1 ? 1 : $page; // Desired page number

        $offset = ($selectedPage - 1) * $itemsPerPage;
        $cities = CdekCourierCity::query()
            ->select('id', 'code', 'active')
            ->offset($offset)
            ->limit($itemsPerPage)
            ->get();;
        foreach ($cities as $city) {
            $package = (new Package())
                ->setWidth(30)
                ->setHeight(20)
                ->setLength(16)
                ->setWeight(1000);
            $tariff = (new Tariff())
                ->setTariffCode(137) // Указывает код тарифа для расчета
                ->setCityCodes(426, $city->code) // Экспресс-метод установки кодов отправителя и получателя// Экспресс-метод установки кодов отправителя и получателя
                ->setPackageWeight(1000)
                ->setPackages($package);
            try {
                $tariff_response = $this->cdek->calculateTariff($tariff);
                if (!$city->active) {
                    $city->update([
                        'active' => true
                    ]);
                }
            } catch (CdekV2RequestException $cdekV2RequestException) {
                if ($city->active) {
                    $city->update([
                        'active' => false
                    ]);
                }
            }
        }
        if ($cities->count()) {
            checkCdekCourierCitiesJob::dispatch($page + 1)->onQueue('check_cities');
        }
    }

    public function updatePvz($page = 0, $region = null)
    {
        if ($page == 0) {
            DB::update('UPDATE `cdek_pvzs` SET `is_updating`=1 WHERE `is_active` = 1');
        }
        $perPage = 50;
        try {
            $requestPvz = (new DeliveryPoints())->setType('PVZ'); //'RU,KZ,BY,AM,KG'
            if ($region) {
                $region_code = CdekRegion::select('id', 'region_code')->where('id', $region)->first()->region_code ?? null;
                if ($region_code) {
                    $requestPvz->setRegionCode($region_code);
                }
            }
            $responsePvz = $this->cdek->getDeliveryPoints($requestPvz);

            if (count($responsePvz)) {
                $responsePvz = array_slice($responsePvz, $page * $perPage, $perPage);
            }
        } catch (CdekV2RequestException $ex) {
            $responsePvz = [];
        }
        if (count($responsePvz)) {
            foreach ($responsePvz as $pvz) {
                $region_obj = null;
                $city_obj = null;
                $pvz_db = CdekPvz::where('code', $pvz->getCode())->first();
                $region_obj = CdekRegion::select('id', 'region_code')->where('region_code', $pvz->getLocation()->getRegionCode())->first();
                $city_obj = CdekCity::select('id', 'code')->where('code', $pvz->getLocation()->getCityCode())->first();
                $cdek_pvz_params = [
                    'code' => $pvz->getCode(),
                    'type' => $pvz->getType(),
                    'country_code' => $pvz->getLocation()->getCountryCode(),
                    'region_code' => $pvz->getLocation()->getRegionCode() ?? '',
                    'region' => $pvz->getLocation()->getRegion() ?? '',
                    'region_id' => $region_obj->id ?? null,
                    'city_code' => $pvz->getLocation()->getCityCode(),
                    'city' => $pvz->getLocation()->getCity(),
                    'city_id' => $city_obj->id ?? null,
                    'fias_guid' => $pvz->getLocation()->getFiasGuid() ?? '',
                    'postal_code' => $pvz->getLocation()->getPostalCode() ?? '',
                    'longitude' => $pvz->getLocation()->getLongitude(),
                    'latitude' => $pvz->getLocation()->getLatitude(),
                    'address' => $pvz->getLocation()->getAddress(),
                    'address_full' => $pvz->getLocation()->getAddress(),
                    'pvz_data' => json_encode([]),
                    'is_active' => true,
                    'is_updating' => false,
                ];
                $cdek_pvz_params = $this->checkNewTerritory($cdek_pvz_params);
                if (!$pvz_db) {
                    CdekPvz::create($cdek_pvz_params);
                } else {
                    $pvz_db->update($cdek_pvz_params);
                }
            }
            UpdateCdekPvzJob::dispatch($page + 1)->onQueue('cdek_pvz');
        } else {
            DB::update('UPDATE `cdek_pvzs` SET `is_active`=0 WHERE `is_updating` = 1');
            DB::update('UPDATE `cdek_pvzs` SET `is_updating`=0 WHERE `is_updating` = 1');
        }
    }

    private function checkNewTerritory($params): array
    {
        $code = $params['code'];
        $new_territory = CdekNewTerritory::where('code', $code)->first();
        if (!$new_territory) {
            return $params;
        }
        $region = CdekRegion::query()->where('region_code', CdekRegion::NEW_TERRITORY_REGION_CODE)->first();
        if (!$region) {
            $region = CdekRegion::create([
                'region' => CdekRegion::NEW_TERRITORY_REGION_NAME,
                'region_code' => CdekRegion::NEW_TERRITORY_REGION_CODE,
                'country' => 'Россия',
                'country_code' => 'RU'
            ]);
        } else {
            $region->update([
                'region' => CdekRegion::NEW_TERRITORY_REGION_NAME
            ]);
        }
        $city = CdekCity::query()->where('code', CdekCity::NEW_TERRITORY_CITY_CODE)->first();
        if (!$city) {
            $city = CdekCity::create([
                "code" => CdekCity::NEW_TERRITORY_CITY_CODE,
                "city" => CdekCity::NEW_TERRITORY_CITY_NAME,
                "country_code" => 'RU',
                "region" => $region->region,
                "region_code" => $region->region_code,
                "region_id" => $region->id,
            ]);
        } else {
            $city->update([
                "code" => CdekCity::NEW_TERRITORY_CITY_CODE
            ]);
        }
        $params['region_code'] = $region->region_code;
        $params['region'] = $region->region;
        $params['region_id'] = $region->id;
        $params['city_code'] = $city->code;
        $params['city'] = $city->city;
        $params['city_id'] = $city->id;
        $params['longitude'] = null;
        $params['latitude'] = null;
        $params['address'] = $new_territory->address;
        $params['address_full'] = $new_territory->address;
        return $params;
    }

    public function prepareOrdersToCdek($order_ids, $user_id)
    {
        $orders = Order::whereIn('id', $order_ids)->get();
        foreach ($orders as $order) {
            // формируем данные для заказа
            $data = $order->data;
            $data_cart = $order->data_cart;
            $data_shipping = $order->data_shipping;
            if ($data_shipping['shipping-code'] == 'cdek' && (!isset($data_shipping['cdek-pvz-id']) || $data_shipping['cdek-pvz-id'] === null)) {
                continue;
            } elseif (
                $data_shipping['shipping-code'] == 'cdek_courier'
                && (!isset($data_shipping['cdek_courier-form-region']) || $data_shipping['cdek_courier-form-region'] === null)
                && (!isset($data_shipping['cdek_courier-form-city']) || $data_shipping['cdek_courier-form-city'] === null)
                && (!isset($data_shipping['cdek_courier-form-street']) || $data_shipping['cdek_courier-form-street'] === null)
                && (!isset($data_shipping['cdek_courier-form-house']) || $data_shipping['cdek_courier-form-house'] === null)
            ) {
                continue;
            }
            if (!in_array($data_shipping['shipping-code'], ['cdek', 'cdek_courier'])) {
                continue;
            }
            $params = [];
            // формируем номер заказа
            $data_cart = mergeItemsById($data_cart);
            $order_number = $order->getOrderNumber() . '_';
            foreach ($data_cart as $item) {
                if (isset($item['raffle']) && (!isset($item['old_price']) || !$item['old_price'])) {
                    continue;
                }
                $order_number .= 'm' . $item['id'] . '-' . $item['qty'] . '_';
            }
            $order_number = trim($order_number, '_');
            $comment = $order_number;
            if (mb_strlen($order_number) > 40) {
                $order_number = $order->getOrderNumber();
            }
            if (mb_strlen($comment) > 65) {
                $comment = $order->getOrderNumber();
            }

            $params['orderNumber'] = $order_number;
            // имя
            if (isset($data['form']['full_name'])) {
                $full_name = $data['form']['full_name'];
            } else {
                $full_name = $data['form']['last_name'] . ' ' . $data['form']['first_name'];
                if (isset($data['form']['middle_name']) && !empty($data['form']['middle_name'])) {
                    $full_name .= ' ' . $data['form']['middle_name'];
                }
            }
            $params['comment'] = $comment;
            $params['full_name'] = $full_name;
            $params['email'] = $data['form']['email'];
            $params['phone'] = $data['form']['phone'];
            if ($data_shipping['shipping-code'] == 'cdek') {
                $params['pvz_id'] = $data_shipping['cdek-pvz-id'];
            } elseif ($data_shipping['shipping-code'] == 'cdek_courier') {
                $params['region'] = $data_shipping['cdek_courier-form-region'];
                $params['city'] = $data_shipping['cdek_courier-form-city'];
                $params['street'] = $data_shipping['cdek_courier-form-street'];
                $params['house'] = $data_shipping['cdek_courier-form-house'];
                $params['flat'] = $data_shipping['cdek_courier-form-flat'];
            }
            $params['cart'] = $data_cart;

            // отправляем запрос
            $uuid = $this->sendOrder($params);
            if (!isset($uuid['success'])) { // если ошибка, то пропускаем
                $order->setStatus('is_processing');
                Log::debug(print_r($uuid, true) . ' order_id' . $order->getOrderNumber());
                continue;
            }
            // сохраняем заказ
            $data_shipping[$data_shipping['shipping-code']]['uuid'] = $uuid['success'];
            $data_shipping[$data_shipping['shipping-code']]['cdek_number'] = $order_number;
            $order->update([
                'data_shipping' => $data_shipping
            ]);
        }
        getCdekTicketsJob::dispatch($order_ids, $user_id)->delay(now()->addMinutes(10))->onQueue('cdek_tickets');
        ShippingLog::create([
            'code' => 'cdek',
            'title' => denum($orders->count(), ['%d заказ', '%d заказа', '%d заказов']) . ' обработано',
            'text' => 'Заказы ' . implode(', ', $orders->pluck('id')->toArray()),
        ]);
    }

    public function getOrder($uuid)
    {
        try {
            $result = $this->cdek->getOrderInfoByUuid($uuid);
        } catch (CdekV2RequestException $ex) {
            $result = ['error' => $ex->getMessage()];
        }
        return $result;
    }

    public function getTickets($order_ids = null, $user_id)
    {
        $orders = Order::select()
            ->where(function ($query) {
                $query->whereNotNull('data_shipping->cdek->uuid')
                    ->orWhereNotNull('data_shipping->cdek_courier->uuid');
            })
            ->doesntHave('tickets')
            ->where(function ($query) {
                $query->whereIn('status',
                    ['cdek_1', 'cdek_created', 'is_processing', 'is_waiting', 'was_processed', 'was_sended_to_store', 'is_assembled', 'cdek_accepted']
                )
                    ->orWhereNull('status');
            })
            ->whereNull('data_shipping->ticket')
            ->whereIn('data_shipping->shipping-code', ['cdek', 'cdek_courier'])
            ->where(function ($query) {
                $query->whereNotNull('data_shipping->cdek->cdek_number')
                    ->orWhereNotNull('data_shipping->cdek_courier->invoice_number');
            });
        if ($order_ids) {
            $orders->whereIn('id', $order_ids);
        }
        $orders = $orders->where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 minutes')))
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        logger()->info(json_encode(['orders_count_in_getTickets' => $orders->count()]));

        if ($orders->count()) {
            return $this->createCdekTicket($orders, $user_id);
        }
    }

    private function createCdekTicket($orders, $user_id)
    {
        logger()->info(json_encode(['orders_count_in_createCdekTicket' => $orders->count()]));

        $user = User::find($user_id);
        $doubles = [];
        $orders_ids = '';
        // формируем группы по 50 заказов
        $orders_ids = implode(',', $orders->pluck('id')->toArray());
        $cdek_orderIds = [];

        $ticket_count = DB::select('SELECT COUNT(*) as count FROM `order_ticket` WHERE `order_id` IN (?);', [$orders_ids]);
        $ticket_count = $ticket_count[0]->count;
        $long_numbers = [];
        $creator = null;

        logger()->info(json_encode(['ticket_count_in_createCdekTicket' => $ticket_count]));

        if ($ticket_count == 0) {
            // формируем группы по 50 заказов
            foreach ($orders as $key => $order) {
                $data_shipping = $order->data_shipping;
                $shipping_code = $data_shipping['shipping-code'];
                // проверяем заказ в сдеке
                if (!isset($data_shipping[$shipping_code]['invoice_number'])) {
                    $check_cdek = $this->getOrder($data_shipping[$shipping_code]['uuid']);
                    if (is_array($check_cdek) && isset($check_cdek['error'])) { // если в заказе ошибка
                        $order->setStatus('has_error');
                        ShippingLog::create([
                            'code' => 'cdek',
                            'title' => 'Найдена ошибка в заказе ' . $order->id,
                            'text' => $check_cdek['error'],
                        ]);
                        unset($data_shipping[$shipping_code]);
                        $order->update([
                            'data_shipping' => $data_shipping
                        ]);
                        unset($orders[$key]);
                        continue;
                    }
                    $data_shipping[$shipping_code]['invoice_number'] = $check_cdek->getCdekNumber();
                    $order->update([
                        'data_shipping' => $data_shipping
                    ]);
                }
            }
            $orders_pvz = Order::select(
                'id',
                'data_shipping->shipping-code as shipping_code',
                'data_shipping->cdek-pvz-id as pvz_id',
                'data_shipping->cdek->invoice_number as invoice_number',
                'data->form->full_name as ful_name',
                'data->form->phone as phone',
                'data_cart',
                'data_shipping'
            )
                ->whereIn('id', $orders->pluck('id'))
                ->whereIn('data_shipping->shipping-code', ['cdek'])
                ->where('data_shipping->cdek-pvz-id', '!=', null)
                ->get();
            $orders_courier = Order::select(
                'id',
                'data_shipping->shipping-code as shipping_code',
                'data_shipping->cdek_courier-form-address as address',
                'data_shipping->cdek_courier->invoice_number as invoice_number',
                'data->form->full_name as ful_name',
                'data->form->phone as phone',
                'data_cart',
                'data_shipping'
            )
                ->whereIn('id', $orders->pluck('id'))
                ->whereIn('data_shipping->shipping-code', ['cdek_courier'])
                ->get();
            $orders = $orders_pvz->merge($orders_courier);
            if (!$orders->count()) {
                return true;
            }
            $pdf = new tFPDF('P', 'pt', [239.94, 297.64]);
            $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
            $pdf->AddFont('DejaVu', 'B', 'DejaVuSansCondensed-Bold.ttf', true);


            foreach ($orders as $order) {
                if (!$order->invoice_number) {
                    continue;
                }
                $recipient_city = '';
                if ($order->pvz_id) {
                    $cdek_pvz = CdekPvz::where('code', $order->pvz_id)->first();
                    if ($cdek_pvz) {
                        $recipient_city = $cdek_pvz->region . ', ' . $cdek_pvz->city . ', ' . $cdek_pvz->address;
                    }
                } elseif ($order->address) {
                    $recipient_city = $order->address;
                }
                $recipient_name = $order->ful_name;
                $recipient_phone = $order->phone;
                $sender_name = 'Нечаева Ольга Андреевна';
                $sender_city = 'Волгоград';
                $cart = $order->data_cart;

                $generator = new BarcodeGeneratorJPG();
                $barcode = $generator->getBarcode($order->invoice_number, $generator::TYPE_CODE_128, 2, 30);
                if (!file_exists(public_path() . '/files/cdek/barcodes')) {
                    mkdir(public_path() . '/files/cdek/barcodes', 0777, true);
                }
                file_put_contents(public_path() . '/files/cdek/barcodes/' . $order->getOrderNumber() . '.jpg', $barcode);

                $pdf->AddPage();
                $pdf->SetFont('DejaVu', '', 8);
                $pdf->Image(public_path() . '/files/cdek/barcodes/' . $order->getOrderNumber() . '.jpg', 10, 10, 239.94 - 20);
                $pdf->SetMargins(10, 10, 10);
                $pdf->SetAutoPageBreak(true, 20);
                //$pdf->SetFont('Arial', 'B', 9);
                $pdf->SetXY(10, 50);
                $pdf->Cell((239.94 - 20) / 2, 14, $order->invoice_number, 0, 0, 'L');
                $pdf->SetFont('DejaVu', 'B', 14);
                $pdf->Cell((239.94 - 20) / 2, 14, substr($order->invoice_number, -4), 0, 0, 'R');
                $pdf->SetXY(10, 65);
                $pdf->SetFont('DejaVu', 'B', 8);
                $pdf->Cell((239.94 - 20) / 2, 14, 'Получатель', 0, 0, 'L');
                $pdf->SetFont('DejaVu', '', 8);
                $pdf->Cell((239.94 - 20) / 2, 14, date('d.m.Y H:i'), 0, 0, 'R');
                $pdf->SetXY(10, 80);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->MultiCell(0, 14, "$recipient_name ($recipient_phone)\n$recipient_city", 1, 'L', true);
                $pdf->SetFont('DejaVu', 'B', 8);
                $pdf->Cell(0, 14, 'Отправитель', 0, 0, 'L');
                $pdf->Ln(15);
                $pdf->setX(10);
                $pdf->SetFont('DejaVu', '', 8);
                $pdf->MultiCell(0, 14, "$sender_name\n$sender_city", 1, 'L', true);
                $pdf->SetFont('DejaVu', '', 10);
                $pdf->MultiCell(0, 14, config('app.name'), 0, 'R', true);
                $pdf->Ln(10);
                $pdf->SetFont('DejaVu', '', 10);
                $pdf->Cell(0, 14, 'Заказ ' . $order->getOrderNumber(), 0, 0, 'L');
                $pdf->Ln(20);
                $pdf->SetFont('DejaVu', '', 11);
                $i = 0;
                $cart_chunk = array_chunk(mergeItemsById($cart), 5);
                $has_builder = false;
                foreach ($cart_chunk as $cart_chunk_item) {
                    $item_code = '';
                    foreach ($cart_chunk_item as $item) {
                        $product = Product::query()->where('id', $item['id'])->select('price')->first();
                        if (isset($item['raffle']) && !$product->price) {
                            continue;
                        }
                        if ($item['id'] > 1000) {
                            $item['id'] -= 1000;
                        }
                        $item_code .= 'm' . $item['id'] . '-' . $item['qty'] . ', ';
                    }
                    $pdf->Cell(10, 10, trim($item_code, ', '), 0);
                    $pdf->Ln(15);
                }

                //        if($has_builder){
                //          foreach($cart as $item){
                //            if(!isset($item['builder'])){
                //              continue;
                //            }
                //            $pdf->AddPage();
                //            $pdf->SetFont('DejaVu','',8);
                //            $pdf->SetMargins(10, 10, 10);
                //            $pdf->SetAutoPageBreak(true, 20);
                //            //$pdf->SetFont('Arial', 'B', 9);
                //            $pdf->SetXY(10, 20);
                //            $pdf->SetFont('DejaVu','B',8);
                //            $pdf->Cell(0, 14, $order->getOrderNumber(), 0, 0, 'L');
                //            $pdf->SetFont('DejaVu','',8);
                //
                //            $pdf->SetFont('DejaVu','',8);
                //            $pdf->setY(35);
                //            $pdf->SetFillColor(255,255,255);
                //            $pdf->MultiCell(0, 14, $item['name'].' ('.$item['model'].' '.$item['qty'].' шт)', 1, 'L', true);
                //            $builder_models = '';
                //            $builder_text = '';
                //            foreach($item['builder'] as $builder_item){
                //              $builder_models .= 'm' . $builder_item['product_id'] . '-' . $builder_item['qty'].', ';
                //              $builder_text .= "– {$builder_item['name']} - {$builder_item['qty']}шт,\n";
                //            }
                //            $pdf->SetFont('DejaVu','',6);
                //            $pdf->SetFillColor(255,255,255);
                //            $pdf->setY(70);
                //            $pdf->MultiCell(0, 14, $builder_text."\n".$builder_models, 1, 'L', true);
                //          }
                //        }
            }
            $file_name = Str::random(8) . '_' . ($creator ?? $user->email ?? '') . '.pdf';
            $directory = '/files/cdek_1/tickets';

            if (!file_exists(public_path() . $directory)) {
                mkdir(public_path() . $directory, 0777, true);
            }
            $result = $pdf->Output('F', public_path() . $directory . '/' . $file_name);

            $ticket = Ticket::create([
                'file_name' => $file_name,
                'file_path' => $directory . '/' . $file_name,
                'items_count' => $orders->count(),
                'delivery_code' => $orders[0]->shipping_code,
            ]);
            foreach ($orders as $order) {
                if ($order->tickets()->count() > 0) { // если дубль, то удаляем наклейки и запускаем отмену
                    $cancel = true;
                    Log::debug('Ошибка ticket заказ ' . $order->getOrderNumber());
                    break;
                }
                $order->tickets()->attach($ticket->id);
                $data_shipping = $order->data_shipping;
                $data_shipping['ticket'] = $directory . '/' . $file_name;
                $order->update([
                    'data_shipping' => $data_shipping
                ]);
            }
            if (isset($cancel) && $cancel) {
                foreach ($orders as $order) {
                    $order->tickets()->detach($ticket->id);
                }
            }
        } else {
            $error = 'Среди заказов есть уже напечатанные';
        }
        if (isset($error) && !empty($error)) {
            $result = ['error' => $error];
        } else {
            $result = ['sussess' => 'Этикетки успешно созданы'];
            if (!empty($doubles)) {
                $result = ['warning' => 'Запрос на создание этикеток успешно отправлен. Были удалены дубли: '];
                foreach ($doubles as $double) {
                    $result['warning'] .= $double . ', ';
                }
                $result['warning'] = trim($result['warning'], ',');
            }
        }
        return $result;
    }

    private function sendOrder($params = array())
    {
        // запрос всех данных формы (Laravel)
        // проверяем заказ
        $order_exists = false;
        try {
            $order = $this->cdek->getOrderInfoByImNumber($params['orderNumber']);

            $statuses = $order->getStatuses();
            $order_exists = true;
            $uuid = $order->getUuid();
            foreach ($statuses as $status) {
                if ($status->getCode() == 'INVALID') {
                    //          $del = $this->cdek->deleteOrder($uuid);
                    //          dd($del);
                    $order_exists = false;
                }
            }
        } catch (CdekV2RequestException $ex) {
            //dd($ex);
        }
        if (!$order_exists) {
            // Создание объекта заказа

            $order = (new \AntistressStore\CdekSDK2\Entity\Requests\Order())
                ->setNumber($params['orderNumber']) // Номер заказа
                ->setType(1);                     // Тип заказа (ИМ)
            if (isset($params['pvz_id'])) {
                $order->setTariffCode(136); // Код тарифа
            } else {
                $order->setTariffCode(137); // Код тарифа
            }
            $order->setComment($params['orderNumber']);
            $order->setDeliveryRecipientCost(0, null); // Стоимость доставки


            // Добавление информации о продавце

            $seller = (new \AntistressStore\CdekSDK2\Entity\Requests\Seller())
                ->setName('Нечаева Ольга Андреевна');
            // ->setName('Яковлева Елена Сергеевна');
            $order->setSeller($seller);
            $order->setShipmentPoint('VLG39');

            // Добавление информации о получателе
            $params['phone'] = preg_replace('/^8/', '+7', $params['phone']);
            if ($params['phone'][0] == '9') {
                $params['phone'] = '+7' . $params['phone'];
            } elseif ($params['phone'][0] == '7') {
                $params['phone'] = '+' . $params['phone'];
            }

            $recipient = (new \AntistressStore\CdekSDK2\Entity\Requests\Contact())
                ->setName($params['full_name'])
                ->setEmail($params['email'])
                ->setPhones($params['phone']);
            $order->setRecipient($recipient);
            if (isset($params['pvz_id'])) {
                $order->setDeliveryPoint($params['pvz_id']);
            } else {
                $order->setRecipientCityCode($params['city']);
                $address = $params['street'] . ', д. ' . $params['house'];
                if ($params['flat']) {
                    $address .= ', кв. ' . $params['flat'];
                }
                $order->setRecipientAddress($address);
            }
            //    // Адрес отправителя только для тарифов "от двери"
            //
            //    $order->setShipmentAddress('ул.Люка Скайоукера, д.1')
            //        ->setShipmentCityCode(1204)
            //        ->setRecipientAddress('ул.Джедаев, д.3')
            //        ->setRecipientCityCode(44)
            //    ;

            // Создаем данные посылки. Место

            $packages = (new \AntistressStore\CdekSDK2\Entity\Requests\Package())
                ->setNumber('1')
                ->setWeight(1000)
                ->setHeight(9.5)
                ->setWidth(23.5)
                ->setLength(16.5);

            // Создаем товары

            $items = [];
            foreach ($params['cart'] as $cart_item) {
                $product = Product::query()->where('id', $cart_item['id'])->select('price')->first();
                if (isset($cart_item['raffle']) && !$product->price) {
                    continue;
                }
                $items[] = (new Item())
                    ->setName($cart_item['name'])
                    ->setWareKey($cart_item['model']) // Идентификатор/артикул товара/вложения
                    ->setPayment(0, 0)
                    ->setCost($cart_item['price']) // Объявленная стоимость товара (за единицу товара)
                    ->setWeight(200) // Вес в граммах
                    ->setAmount($cart_item['qty']) // Количество
                ;
            }
            $packages->setItems($items);
            $order->setPackages($packages);


            // Заказ подготовлен отправляем в ранее объявленный клиент

            try {
                $response = $this->cdek->createOrder($order);
                $result = ['success' => $response->getEntityUuid()];
            } catch (CdekV2RequestException $ex) {
                $result = ['error' => $ex->getMessage() . ' номер заказа ' . $params['orderNumber']];
                ShippingLog::create([
                    'code' => 'cdek',
                    'title' => 'Ошибка отправки заказа',
                    'text' => $ex->getMessage() . ' номер заказа ' . $params['orderNumber'],
                ]);
            }
        } else {
            $result = ['error' => 'Заказ уже существует ' . $order->getNumber() . ' номер заказа ' . $params['orderNumber']];
            ShippingLog::create([
                'code' => 'cdek',
                'title' => 'Ошибка отправки заказа',
                'text' => 'Заказ уже существует ' . $order->getNumber() . ' номер заказа ' . $params['orderNumber'],
            ]);
        }
        return $result;
    }
}
