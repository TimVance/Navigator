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