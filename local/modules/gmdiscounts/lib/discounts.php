<?php

namespace GM;

use Bitrix\Crm\Integration\Main\UISelector\Handler;
use Bitrix\Sender\Integration\Im\Notification;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\
{Loader, Application, Data\Cache, EventManager};

class Discounts
{

    private static $module = 'gmdiscounts';

    public static function calcDiscounts() {
        $result = [];
        if (Loader::IncludeModule(self::$module))
        {
            $iblock = \COption::GetOptionString(self::$module, "iblock");
            if (!empty($_REQUEST["action"]) && !empty($iblock)) {
                if ($_REQUEST["action"] == 'start') {
                    $result["cnt"] = self::getCntElements($iblock);
                    if (intval($result["cnt"]) > 0) {
                        $result["last"] = self::calcDiscountByStep($iblock);
                        $result["step_number"] = 1;
                        $step = !empty(\COption::GetOptionString(self::$module, "step")) ? \COption::GetOptionString(self::$module, "step") : 100;
                        $result["progress_cnt"] = intval($step) * $result["step_number"];
                    }
                } elseif ($_REQUEST["action"] == 'process') {
                    $result["cnt"] = self::getCntElements($iblock);
                    if (intval($result["cnt"]) > 0) {
                        $result["last"] = self::calcDiscountByStep($iblock, $_REQUEST["lastId"]);
                        $result["step_number"] = intval($_REQUEST["step_number"]) + 1;
                        $step = !empty(\COption::GetOptionString(self::$module, "step")) ? \COption::GetOptionString(self::$module, "step") : 100;
                        $result["progress_cnt"] = intval($step) * $result["step_number"];
                    }
                }
            }
        }
        return json_encode($result);
    }

    public static function getCntElements($iblock) {
        $arFilter = Array("IBLOCK_ID" => IntVal($iblock), "ACTIVE" => "Y");
        $cnt = \CIBlockElement::GetList([], $arFilter, [], [], []);
        return $cnt;
    }

    public static function calcDiscountByStep($iblock, $last = 0) {
        $list = [];
        $arSelect = Array("ID", "IBLOCK_ID");
        $arFilter = Array("IBLOCK_ID" => IntVal($iblock), "ACTIVE" => "Y");
        if (!empty($last)) {
            $arFilter[">ID"] = $last;
        }
        $res = \CIBlockElement::GetList(
            Array("ID" => "ASC"),
            $arFilter,
            false,
            Array(
                "nPageSize" => !empty(\COption::GetOptionString(self::$module, "step")) ? \COption::GetOptionString(self::$module, "step") : 100
            ),
            $arSelect
        );
        $last_id = 0;
        Handlers::$handlerDisallow = true;
        while($ob = $res->GetNext()) {
            $last_id = self::calcDiscountById($iblock, $last = 0, $ob["ID"]);
        }
        Handlers::$handlerDisallow = false;
        return $last_id;
    }

    public static function calcDiscountById($iblock, $last = 0, $id) {
        $allProductPrices = \Bitrix\Catalog\PriceTable::getList([
            "filter" => [
                "PRODUCT_ID" => $id,
            ]
        ])->fetchAll();
        $prices = [];
        foreach ($allProductPrices as $price) {
            $prices[$price["CATALOG_GROUP_ID"]] = floatval($price["PRICE"]);
        }
        $param1 = \COption::GetOptionString(self::$module, "price1");
        $param2 = \COption::GetOptionString(self::$module, "price2");
        $skidka = floatval($prices[$param1]) - floatval($prices[$param2]);
        \CIBlockElement::SetPropertyValuesEx($id, $iblock, [
            \COption::GetOptionString(self::$module, "write") => $skidka
        ]);
        return $id;
    }

}
