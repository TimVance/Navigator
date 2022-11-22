<?php

// Delivery
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('sale', 'onSaleDeliveryHandlersClassNamesBuildList', 'addCustomDeliveryServices');
function addCustomDeliveryServices(\Bitrix\Main\Event $event)
{
    $result = new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            '\GM\GMDeliveryHandler' => '/local/php_interface/classes/GMDeliveryHandler.php',
        )
    );
    return $result;
}

// Dadata
CModule::AddAutoloadClasses(
    '',
    array(
        '\GM\Dadata' => '/local/php_interface/classes/Dadata.php',
    )
);


// Синхронизация баллов с 1С
use Bitrix\Main\Web\HttpClient;
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'main',
    'OnProlog',
    'getUserBalance'
);
function getUserBalance()
{
    global $APPLICATION;
    if ($APPLICATION->GetCurPage(false) == '/basket/') {
        global $USER;
        if (!empty($USER->getID())) {

            $currentUser = CUser::GetByID($USER->getID());
            $arUser = $currentUser->Fetch();

            if (!empty($arUser["PERSONAL_PHONE"])) {
                $phone = '8'.str_replace(['(', ')', '+7', ' ', '-'], '', $arUser["PERSONAL_PHONE"]);

                $url = 'https://80.91.195.37/NVG_KA2_COPY/ru_RU/hs/loyaltyservice/getclientbonuses?PhoneNumber='.$phone;
                $username = 'serviceloyality';
                $password = 'Si2jikim';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_REFERER, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $data = json_decode($result, true);

                if ($data["PhoneNumber"] == $phone) {
                    CModule::IncludeModule('logictim.balls');
                    $userBonus = intval(cHelper::UserBallance($USER->getID()));
                    if ($userBonus != intval($data["Bonus"])) {
                        if ($userBonus > intval($data["Bonus"])) {
                            $arFields = array(
                                "MINUS_BONUS" => intval($userBonus) - intval($data["Bonus"]),
                                "USER_ID" => $USER->getID(),
                                "OPERATION_TYPE" => 'USER_BALLANCE_CHANGE',
                                "OPERATION_NAME" => 'Списание',
                                "ORDER_ID" => '',
                                "DETAIL_TEXT" => '',
                            );
                            logictimBonusApi::MinusBonus($arFields);
                        } else {
                            $arFields = array(
                                "ADD_BONUS" => intval($data["Bonus"]) - intval($userBonus),
                                "USER_ID" => $USER->getID(),
                                "OPERATION_TYPE" => 'USER_BALLANCE_CHANGE',
                                "OPERATION_NAME" => 'Начисление',
                                "ORDER_ID" => '',
                                "DETAIL_TEXT" => '',
                            );
                            logictimBonusApi::AddBonus($arFields);
                        }
                    }

                    // Смена групп
                    $groups = [
                        'STANDART' => 6,
                        'VIP' => 9,
                        'SPECIAL' => 10,
                    ];
                    $arGroups = CUser::GetUserGroup($USER->getID());
                    $newGroups = [];
                    foreach ($arGroups as $itemGroup) {
                        if (!in_array($itemGroup, $groups)) {
                            $newGroups[] = $itemGroup;
                        }
                    }
                    $newGroups[] = $groups[$data["LevelDiscount"]];
                    CUser::SetUserGroup($USER->getID(), $newGroups);
                }
            }
        }
    }

}