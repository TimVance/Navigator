<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
/**
 * Class
 */
class gmdiscounts extends \CModule
{
    /**
     * @var string
     */
    const MODULE_ID = 'gmdiscounts';
    /**
     * @var string
     */
    public $MODULE_ID = self::MODULE_ID;
    /**
     * @var string
     */
    public $MODULE_VERSION;
    /**
     * @var string
     */
    public $MODULE_VERSION_DATE;
    /**
     * @var string
     */
    public $MODULE_NAME;
    /**
     * @var string
     */
    public $MODULE_DESCRIPTION;
    /**
     * @var bool
     */
    const moduleClassEvents = 'GM\\Handlers';
    const moduleMethodEvents = 'recalcDiscount';
    public $errors = false;
    /**
     * Инициализация модуля
     */
    public function __construct()
    {
        Loc::loadMessages(__FILE__);
        $moduleVersion = array();
        include(realpath(__DIR__) . '/version.php');
        $this->MODULE_VERSION = $moduleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $moduleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESCRIPTION');

        $this->PARTNER_NAME = "Good Morning";
        $this->PARTNER_URI = "https://goodmg.ru/";
    }
    /**
     * Регистрация модуля в БД
     *
     * @return bool
     * @throws Exception
     */
    public function InstallDB()
    {
        global $errors;
        $errors = false;
        if (!empty($errors)) {
            throw new \Exception(implode('', $errors));
        }
        \Bitrix\Main\ModuleManager::registerModule(self::MODULE_ID);
        return true;
    }
    /**
     * Удалить модуль из БД
     *
     * @param array $arParams
     * @return bool
     */
    public function UnInstallDB($arParams = Array())
    {
        global $errors;
        \COption::RemoveOption(self::MODULE_ID);
        \Bitrix\Main\ModuleManager::unRegisterModule(self::MODULE_ID);
        return true;
    }
    /**
     * Инициализация установки модуля
     *
     * @throws Exception
     */
    public function DoInstall()
    {
        global $USER, $APPLICATION;
        if ($USER->IsAdmin()) {
            if (!IsModuleInstalled(self::MODULE_ID)) {
                $this->InstallDB();
                $this->InstallEvents();
                $this->InstallFiles();
                $GLOBALS['errors'] = $this->errors;
                $APPLICATION->IncludeAdminFile(Loc::getMessage('INSTALL_TITLE'), realpath(__DIR__) . '/step.php');
            }
        }
    }
    /**
     * Инициализация удаления модуля
     */
    public function DoUninstall()
    {
        global $USER, $APPLICATION, $step;
        if ($USER->IsAdmin()) {
            $this->UnInstallDB(array());
            $this->UnInstallFiles();
            $this->UnInstallEvents([]);
            $GLOBALS['errors'] = $this->errors;
            $APPLICATION->IncludeAdminFile(Loc::getMessage('UNINSTALL_TITLE'), realpath(__DIR__) . '/unstep.php');
        }
    }

    function InstallEvents($arParams = array())
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler("iblock", "OnAfterIBlockAdd", self::MODULE_ID, self::moduleClassEvents, self::moduleMethodEvents);
        $eventManager->registerEventHandler("iblock", "OnAfterIBlockElementUpdate", self::MODULE_ID, self::moduleClassEvents, self::moduleMethodEvents);
    }

    function UnInstallEvents($arParams = array())
    {
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler("iblock", "OnAfterIBlockAdd", self::MODULE_ID, self::moduleClassEvents, self::moduleMethodEvents);
        $eventManager->unRegisterEventHandler("iblock", "OnAfterIBlockElementUpdate", self::MODULE_ID, self::moduleClassEvents, self::moduleMethodEvents);
    }
}
