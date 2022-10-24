<?
use Bitrix\Main\Localization\Loc;
IncludeModuleLangFile(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_landing",
    "section" => "createblocks",
    "sort" => 1000,
    "text" => Loc::getMessage("CREATEBLOCK_MODULE_MENU"),
    "title"=> Loc::getMessage("CREATEBLOCK_MODULE_MENU"),
    "url" => "/local/modules/renins/admin/create_blocks/index.php",
    "icon" => "sale_menu_icon_crm",
    "items" => array()
);

return $aMenu;
?>
