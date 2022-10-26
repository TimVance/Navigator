<?php

// Подгрузка классов модуля
Bitrix\Main\Loader::registerAutoLoadClasses("gmdiscounts", array(
    '\GM\Discounts' => 'lib/discounts.php',
    '\GM\Handlers'  => 'lib/handlers.php',
));
