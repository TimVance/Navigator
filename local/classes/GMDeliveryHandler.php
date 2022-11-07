<?php

namespace classes;

use Bitrix\Main\Error;

class GMDeliveryHandler extends \Bitrix\Sale\Delivery\Services\Base
{
    protected static $isCalculatePriceImmediately = true;
    protected static $whetherAdminExtraServicesShow = false;
    protected static $yandex_taxi_url = 'https://b2b.taxi.yandex.net/b2b/cargo/integration/v1/check-price';

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

    protected function calculateConcrete(\Bitrix\Sale\Shipment $shipment = null)
    {

        $order = $shipment->getCollection()->getOrder(); // заказ
        $props = $order->getPropertyCollection();
        $locationCode = $props->getAddress()->getValue(); // местоположение

        $request = [];
        $request["items"][] = [
            "quantity" => 1,
            "size" => [
                "height" => 0.1,
                "length" => 0.1,
                "width" => 0.1,
            ],
            "weight" =>  2
        ];
        $request["client_requirements"] = [
            "assign_robot" =>  false,
            "cargo_loaders" =>  0,
            "cargo_options" =>  ["auto_courier"],
            "cargo_type" =>  ["lcv_m"],
            "pro_courier" =>  false,
            "taxi_class" =>  "express",
        ];
        $request["route_points"][] = [
            "coordinates" => [37.588074, 55.733924],
            "coordinates" => [37.588074, 55.733924],
        ];
        $request["skip_door_to_door"] = false;

        $httpClient         = new \Bitrix\Main\Web\HttpClient();
        $httpClient->setHeader('Authorization', 'Bearer '.'y0_AgAAAABaqjygAAVM1QAAAADPbMhkyjL6b_jJRoOKpKkbf3_iT8nhfLc');
        $response           = $httpClient->post(self::$yandex_taxi_url, json_encode($request));
        $array = [];
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
        $result = new \Bitrix\Sale\Delivery\CalculationResult();
        if (!empty($array["price"])) {
            $result->setDeliveryPrice(
                roundEx(
                    $array["price"],
                    SALE_VALUE_PRECISION
                )
            );
            $result->setPeriodDescription('4-7 days');
            return $result;
        } else {
            return ($result->addError(new Error('The company no longer exists')));
        }
    }
}