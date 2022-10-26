<?php

namespace GM;

class Handlers
{

    public static $handlerDisallow = false;
    private static $module = 'gmdiscounts';

    public static function recalcDiscount($arFields)
    {
        if (
            self::$handlerDisallow ||
            \COption::GetOptionString(self::$module, "iblock") != $arFields["IBLOCK_ID"] ||
            \COption::GetOptionString(self::$module, "autocalc") != "Y"
        ) return;

        Handlers::$handlerDisallow = true;
        $result = Discounts::calcDiscountById($arFields["IBLOCK_ID"], $last = 0, $arFields["ID"]);
        Handlers::$handlerDisallow = false;

        return $arFields;
    }
}