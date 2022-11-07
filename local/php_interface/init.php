<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('sale', 'onSaleDeliveryHandlersClassNamesBuildList', 'addCustomDeliveryServices');

function addCustomDeliveryServices(\Bitrix\Main\Event $event)
{
    $result = new \Bitrix\Main\EventResult(
        \Bitrix\Main\EventResult::SUCCESS,
        array(
            '\classes\GMDeliveryHandler' => '/local/classes/GMDeliveryHandler.php'
        )
    );

    return $result;
}

if (!empty($_COOKIE["test"])) {
    print_r($_COOKIE["test"]);
    exit();
}