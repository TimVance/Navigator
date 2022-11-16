<?php

namespace GM;

use Bitrix\Main\Error;
use \Bitrix\Main\Data\Cache;

class GMDeliveryHandler extends \Bitrix\Sale\Delivery\Services\Base
{
    protected static $isCalculatePriceImmediately   = true;
    protected static $whetherAdminExtraServicesShow = false;
    protected static $yandex_taxi_url               = 'https://b2b.taxi.yandex.net/b2b/cargo/integration/v1/check-price';
    protected static $dadata_token                  = '4b6e202a8fdd932f5e6dde08f59994751d2dc096';
    protected static $dadata_secret                 = '519bde754695001d451ca93df881f6fbac5b9be4';
    protected static $yandex_coordinates            = [92.897362, 56.020034];
    protected static $yandex_token                  = 'y0_AgAAAABaqjygAAVM1QAAAADPbMhkyjL6b_jJRoOKpKkbf3_iT8nhfLc';
    protected static $cach_deliviery                 = 30; // Кеш доставки

    public function __construct(array $initParams)
    {
        parent::__construct($initParams);
    }

    public static function getClassTitle()
    {
        return 'Яндекс.Такси';
    }

    public static function getClassDescription()
    {
        return 'Доставка сервисом яндекс такси до двери';
    }

    public function isCalculatePriceImmediately()
    {
        return self::$isCalculatePriceImmediately;
    }

    public static function whetherAdminExtraServicesShow()
    {
        return self::$whetherAdminExtraServicesShow;
    }

    protected function getConfigStructure()
    {
        $result = array(
            'MAIN' => array(
                'TITLE'       => 'Основные',
                'DESCRIPTION' => 'Основные настройки',
                'ITEMS'       => array(
                    'API_KEY'        => array(
                        'TYPE' => 'STRING',
                        'NAME' => 'Ключ API',
                    ),
                    'TEST_MODE'      => array(
                        'TYPE'    => 'Y/N',
                        'NAME'    => 'Тестовый режим',
                        'DEFAULT' => 'N'
                    ),
                    'PACKAGING_TYPE' => array(
                        'TYPE'    => 'ENUM',
                        'NAME'    => 'Тип упаковки',
                        'DEFAULT' => 'BOX',
                        'OPTIONS' => array(
                            'BOX' => 'Коробка',
                            'ENV' => 'Конверт',
                        )
                    ),
                )
            )
        );
        return $result;
    }

    private static function getGeoDadata($locationCode) {
        $dadata = new \GM\Dadata(self::$dadata_token, self::$dadata_secret);
        $dadata->init();
        $result_geo = $dadata->clean("address", $locationCode);
        $dadata->close();
        \CEventLog::Add(array(
            "SEVERITY"      => "MAIN",
            "AUDIT_TYPE_ID" => "dadata",
            "MODULE_ID"     => "main",
            "ITEM_ID"       => 1,
            "DESCRIPTION"   => print_r($result_geo, true),
        ));
        return $result_geo;
    }

    private static function getPriceFromYandexTaxi($result_geo) {
        $request                        = [];
        $request["items"][]             = [
            "quantity" => 1,
            "size"     => [
                "height" => 0.1,
                "length" => 0.1,
                "width"  => 0.1,
            ],
            "weight"   => 2
        ];
        $request["client_requirements"] = [
            "assign_robot"  => false,
            "cargo_loaders" => 0,
            "cargo_options" => ["auto_courier"],
            "cargo_type"    => ["lcv_m"],
            "pro_courier"   => false,
            "taxi_class"    => "express",
        ];
        $request["route_points"][]["coordinates"] = self::$yandex_coordinates;
        $request["route_points"][]["coordinates"] = [floatval($result_geo[0]["geo_lon"]), floatval($result_geo[0]["geo_lat"])];
        $request["skip_door_to_door"]   = false;

        \CEventLog::Add(array(
            "SEVERITY"      => "MAIN",
            "AUDIT_TYPE_ID" => "update_order",
            "MODULE_ID"     => "main",
            "ITEM_ID"       => 1,
            "DESCRIPTION"   => print_r($request, true),
        ));

        $httpClient = new \Bitrix\Main\Web\HttpClient();
        $httpClient->setHeader('Authorization', 'Bearer ' . self::$yandex_token);
        $response = $httpClient->post(self::$yandex_taxi_url, json_encode($request));
        $array    = [];
        if ($response) {
            $array = json_decode($response, true);
        }

        \CEventLog::Add(array(
            "SEVERITY"      => "MAIN",
            "AUDIT_TYPE_ID" => "update_order",
            "MODULE_ID"     => "main",
            "ITEM_ID"       => 1,
            "DESCRIPTION"   => print_r($array, true),
        ));
        return $array;
    }

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment = null)
    {
        global $USER;
        $order        = $shipment->getCollection()->getOrder(); // заказ
        $props        = $order->getPropertyCollection();


        \CEventLog::Add(array(
            "SEVERITY"      => "MAIN",
            "AUDIT_TYPE_ID" => "address",
            "MODULE_ID"     => "main",
            "ITEM_ID"       => 1,
            "DESCRIPTION"   => print_r($props->getArray(), true),
        ));


        $locationCode = '';
        try {
            $locationCode = $props->getAddress()->getValue(); // местоположение
        } catch (\Error $e) {}

        if (!empty($locationCode)) {

            // Cash price for locatiod code
            $cache = Cache::createInstance();
            $cachePath = 'gmyandex';
            $cacheTtl = self::cach_deliviery;
            $cacheKey = $USER->GetID().$locationCode;
            if ($cache->initCache($cacheTtl, $cacheKey, $cachePath))
            {
                $array = $cache->getVars();
            }
            elseif ($cache->startDataCache())
            {
                // Get geo from dadata
                if (!empty($locationCode)) {
                    $result_geo = self::getGeoDadata($locationCode);

                    if (!empty($result_geo)) {
                        // Get Price from Yandex Taxi
                        $array = self::getPriceFromYandexTaxi($result_geo);
                    }
                } else {
                    $cacheInvalid = false;
                    if ($cacheInvalid)
                    {
                        $cache->abortDataCache();
                    }
                }

                // Всё хорошо, записываем кеш
                $cache->endDataCache($array);
            }

            // Calculate
            $result = new \Bitrix\Sale\Delivery\CalculationResult();
            if (!empty($array["price"])) {
                $result->setDeliveryPrice(
                    roundEx(
                        $array["price"],
                        SALE_VALUE_PRECISION
                    )
                );
                $distance = (float) $array["distance_meters"] / 1000;
                $result->setPeriodDescription('Доcтавка от склада г Красноярск, улица Белинского 3 до '.$locationCode.' ('.round($distance, 1).' км)');
                return $result;
            } else {
                return ($result->addError(new Error('The company no longer exists')));
            }
        }
        return new \Bitrix\Sale\Delivery\CalculationResult();
    }
}