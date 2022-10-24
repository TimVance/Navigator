<?php

namespace Renins;

use Bitrix\Crm\Integration\Main\UISelector\Handler;
use Bitrix\Sender\Integration\Im\Notification;
use Bitrix\Main\
{Loader, Application, Data\Cache, EventManager};
use Bitrix\Main\Type\DateTime;
class Helper
{
    //Отправка уведомления пользователю
    public static function sendNotification($userId, $msg) {
        Notification::create()
            ->withMessage($msg)
            ->setTo([$userId])
            ->send();
    }

    /**
     * @param string $text
     * @param int $to
     * @param int $from
     * Отправляет
     */
    public static function sendNotify($text,$to,$from=1){

        $arMessage = $text;
        $arMessageFields = array(
            // получатель
            "TO_USER_ID" => $to,
            // отправитель
            "FROM_USER_ID" =>$from,
            "NOTIFY_TYPE" => IM_NOTIFY_FROM,
            "NOTIFY_MODULE" => "blog",
            "NOTIFY_MESSAGE" => $text
        );

        \CIMNotify::Add($arMessageFields);
    }

    /**
     * Получает ID инфоблока по коду ИБ
     * @param string $code
     * @return ID
     */

    public static function getIdbyCode(string $code)
    {
        $res = \CIBlock::GetList(
            array(),
            array(
                "CODE" => $code,
                'CHECK_PERMISSIONS' => 'N'
            ),
            false
        );
        $ID=false;
        while ($ar_res = $res->Fetch()) {
            $ID = $ar_res['ID'];
        }
        return $ID;
    }

    /**
     * Получаем ID раздела по CODE
     * @param string $iblockCode
     * @param string $code
     * @return ID
     */
    public static function getSectionIDbyCode(string $iblockCode, string $code){
        $res = \CIBlockSection::GetList(array(), array('IBLOCK_CODE' => $iblockCode, 'CODE' => $code));
        $section = $res->Fetch();
        return $section["ID"];
    }

    /**
     * Получаем разделы вместе с фильтром
     */
    public static function getSectionsWithFilter($iBlockCode,$userFilter=array(),$sort=Array('SORT'=>'ASC')){
        $return=array();
        $IBlockID=\Renins\Helper::getIdbyCode($iBlockCode);
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_CREATE","DESCRIPTION","UF_*","PICTURE","IBLOCK_SECTION_ID","DEPTH_LEVEL");
        $arFilter = Array("IBLOCK_ID"=>$IBlockID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
        $arFilter=array_merge($arFilter,$userFilter);
        $res = \CIBlockSection::GetList(Array('SORT'=>'ASC'), $arFilter,false, $arSelect);
        while($ob = $res->GetNext()){

            $return[$ob['ID']]=$ob;
            unset($ret);
        }
        return $return;
    }

    /**
     * Получаем Iblock с фильтром
     * @param $iBlockCode
     * @param array $userFilter
     * @return mixed
     */
    public static function getIblockDataWithFilter($iBlockCode,$userFilter=array(),$sort=Array('SORT'=>'ASC')){
        $IBlockID=\Renins\Helper::getIdbyCode($iBlockCode);
        $arSelect = Array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DATE_ACTIVE_FROM","PREVIEW_TEXT","DETAIL_TEXT","IBLOCK_SECTION_ID","PROPERTY_*");
        $arFilter = Array("IBLOCK_ID"=>$IBlockID, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
        $arFilter=array_merge($arFilter,$userFilter);
        $res = \CIBlockElement::GetList($sort, $arFilter, false, false, $arSelect);
        while($ob = $res->GetNextElement()){
            $arFields = $ob->GetFields();
            $ret=$arFields;
            $arProps = $ob->GetProperties();
            $ret['PROPS']=$arProps;
            $return[$arFields['ID']]=$ret;
            unset($ret);
        }
        return $return;
    }
    /*
     * Получаем id HLблока по названию
     * @param $name
     * @return bool|mixed
     */
    public static function getIdbyHLBlockName($name){
        $result = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('=NAME'=>$name)));
        if($row = $result->fetch())
        {
            return $row["ID"];
        }
        else{
            return false;
        }
    }

    /**
     * Добавляем данные в ИБ
     * @param $iblockID
     * @param $dataArray
     * @param bool $sectionID
     * @return array
     */
    public static function addIblockData($iblockID,$dataArray,$sectionID=false){
        global $USER;
        $name=$dataArray['NAME'];
        $now=new DateTime();
        unset($dataArray['NAME']);
        if(isset($dataArray['PREVIEW_TEXT'])){
            $preview_text=$dataArray['PREVIEW_TEXT'];
            unset($dataArray['PREVIEW_TEXT']);
        }
        else{
            $preview_text='';
        }
        $data = Array(
            "CREATED_BY" => $USER->GetID(),
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => $sectionID,
            'DATE_ACTIVE_FROM'=>$now->toString(),
            "IBLOCK_ID" => $iblockID,
            "PROPERTY_VALUES" => $dataArray,
            'PREVIEW_TEXT'=>$preview_text,
            "NAME" => $name,
            "ACTIVE" => "Y",
        );
        $el = new \CIBlockElement;
        $id = $el->Add($data);
        if($id) {
            return array('STATUS'=>'OK','ID'=>$id);
        }
        else {
            return array('STATUS'=>'ERROR','TEXT'=>$el->LAST_ERROR);
        }
    }

    /**
     * Добавляем в HL блок
     * @param $HLBLOCK_CODE
     * @param $addArray
     * @return bool|string
     * @throws \Bitrix\Main\SystemException
     */
    public static function addHLData($HLBLOCK_CODE, $addArray)
    {
        $HLBLOCK_ID=\Renins\Helper::getIdbyHLBlockName($HLBLOCK_CODE);
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($HLBLOCK_ID)->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();


        $result = $entity_data_class::add($addArray);

        if ($result->isSuccess()) {
            return true;
        } else {
            return 'При добавлении возникла ошибка: ' . implode(', ', $result->getErrors());
        }
    }

    /**
     * Возвращает группу элемента
     * @param $elemID
     * @return mixed
     */
    public static function getSectionForElement($elemID){
        $db_groups = \CIBlockElement::GetElementGroups($elemID, false);
        $ar_group = $db_groups->Fetch();
        return $ar_group["ID"];
    }
    /**
     * Обновляем свойство или массив свойств
     * @param $ELEMENT_ID
     * @param $data
     * @return mixed
     */
    public static function updateProp($ELEMENT_ID,$data){
        return \CIBlockElement::SetPropertyValuesEx($ELEMENT_ID, false, $data);
    }

    /**
     * Меняем основные данные в ИБ (без свойств)
     * @param $iblockID
     * @param $dataArray
     * @param bool $sectionID
     * @return array
     */
    public static function editIblockData($iblockID,$dataArray,$elemID){
        global $USER;
        $name=$dataArray['NAME'];
        unset($dataArray['NAME']);
        if(isset($dataArray['PREVIEW_TEXT'])){
            $preview_text=$dataArray['PREVIEW_TEXT'];
            unset($dataArray['PREVIEW_TEXT']);
        }
        else{
            $preview_text='';
        }
        $data = Array(
            "CREATED_BY" => $USER->GetID(),
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_ID" => $iblockID,
            'PREVIEW_TEXT'=>$preview_text,
            "NAME" => $name,
            "ACTIVE" => "Y",
        );
        $el = new \CIBlockElement;
        $id = $el->Update($elemID,$data);
        if($id) {
            return array('STATUS'=>'OK','ID'=>$elemID);
        }
        else {
            return array('STATUS'=>'ERROR','TEXT'=>$el->LAST_ERROR);
        }
    }
    /**
     * Получает id enum
     * @param $val
     * @param $iblockCode
     * @param $enumName
     * @return mixed
     */
    public function getEnumIDbyName($val,$iblockCode,$enumName){

        $IblockID=self::getIdbyCode($iblockCode);
        $values=self::getPropEnums($IblockID,$enumName);
        $ID=false;
        foreach ($values as $value){
            if($value['VALUE']==$val){
                $ID=$value['ID'];
            }
        }
        return $ID;

    }
    /**
     * варианты свойства типа "список"
     * @param int $iblockID идентификатор инфоблока
     * @param string $propCode код свойства
     * @param string $codeKey код поля для ключа
     * @return array варианты списка xml_id => array
     */
    public static function getPropEnums( $iblockID, $propCode, $codeKey = 'XML_ID' )
    {
        $arResult = array();

        if( empty( $iblockID ) || empty( $propCode ) )
            return $arResult;

        $cache = Cache::createInstance();
        $f = __FUNCTION__;
        $cacheTime = 60 * 60 * 24 * 365;
        $cacheId = md5( serialize( array( $f, $iblockID, $propCode, $codeKey ) ) );
        $cacheDir = '/s1/props-enum/' . $f . '/';

        if( $cache->initCache( $cacheTime, $cacheId, $cacheDir ) )
            $arResult = $cache->getVars();
        elseif( $cache->startDataCache() )
        {
            $cache_manager = Application::getInstance()->getTaggedCache();
            $cache_manager->startTagCache( $cacheDir );

            Loader::includeModule( 'iblock' );
            $dbEnums = \CIBlockPropertyEnum::GetList(
                array(
                    'SORT' => 'ASC',
                    'VALUE' => 'ASC'
                ),
                array(
                    'IBLOCK_ID' => $iblockID,
                    'CODE' => $propCode
                )
            );
            while( $arEnum = $dbEnums->Fetch() )
                $arResult[ $arEnum[ $codeKey ] ] = $arEnum;

            $cache_manager->registerTag( 'iblock_id_' . $iblockID );
            $cache_manager->endTagCache();
            $cache->endDataCache( $arResult );
        }
        return $arResult;
    }

    /**
     * Удаляем элемент
     * @param $itemID
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    public static function delItem($itemID)
    {
        Loader::includeModule('iblock');
        if (!\CIBlockElement::Delete($itemID)) {
            return false;
        } else {
            return true;
        }
    }

    public static function RusEnding($n, $n1, $n2, $n5)
    {
        if ($n >= 11 and $n <= 19) return $n5;
        $n = $n % 10;
        if ($n == 1) return $n1;
        if ($n >= 2 and $n <= 4) return $n2;
        return $n5;
    }

    public static function RusEndingDays($n)
    {
        return self::RusEnding($n, "день", "дня", "дней");
    }

    public static function diffDate($date1, $date2)
    {
        $from = strtotime($date1);
        $to = strtotime($date2);
        $to = strtotime(date("Y-m-d 23:59:59", $to));
        $datediff = $to - $from;
        $days = ceil($datediff / (60 * 60 * 24));
        return $days;
    }

    /**
     * Получаем ID группы по символьному коду группы
     * @param $code
     * @return bool
     */
    public static function getGroupByCode($code)
    {
        $rsGroups = \CGroup::GetList ($by = "c_sort", $order = "asc", Array ("STRING_ID" => $code));
        $group=$rsGroups->Fetch();

        if(isset($group['ID'])){
            return $group['ID'];
        }
        else{
            return false;
        }
    }

    /**
     * Получаем все уникальные функции
     * @return array
     */

    public static function getFunctionsList(){
        $HLBlockID=\Renins\Helper::getIdbyHLBlockName('Functions');
        $arr = array(
            'filter'=> ['*']
        );
        $functions_data = \Renins\Helper::getHLDatawithFilter($HLBlockID, $arr);
        $result = [];
        foreach ($functions_data as $item) {
            $result[] = $item["UF_FUNCTION"];
        }
        return $result;
    }


    /**
     * Получаем все уникальные города
     * @return array
     */
    public static function getUserCitiesList(){
        $HLBlockID=\Renins\Helper::getIdbyHLBlockName('BossCities');
        $arr = array(
            'filter'=> ['*']
        );
        $functions_data = \Renins\Helper::getHLDatawithFilter($HLBlockID, $arr);
        $result = [];
        foreach ($functions_data as $item) {
            $result[] = $item["UF_CITY"];
        }
        return $result;
    }

    /**
     * Получаем все орг.структуру
     * @return array
     */
    public static function getCompanyDepartments(){
        $IBLOCK_ID=self::getIdbyCode('departments');
        $arFilter = array(
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $IBLOCK_ID,
            'GLOBAL_ACTIVE'=>'Y',
        );
        $arSelect = array('IBLOCK_ID','ID','NAME','DEPTH_LEVEL','IBLOCK_SECTION_ID');
        $arOrder = array('DEPTH_LEVEL'=>'ASC','SORT'=>'ASC');
        $rsSections = \CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);
        $sectionLinc = array();
        $arResult['ROOT'] = array();
        $sectionLinc[0] = &$arResult['ROOT'];
        while($arSection = $rsSections->GetNext()) {
            $sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['ID']] = $arSection;
            $sectionLinc[$arSection['ID']] = &$sectionLinc[intval($arSection['IBLOCK_SECTION_ID'])]['CHILD'][$arSection['ID']];
        }
        return $arResult;
    }

    /**
     * Получаем HL блок с фильтром
     * @param $name
     * @return bool|array
     */
    public static function getHLDatawithFilter($HLBLOCK_ID,$filter){
        $hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getById($HLBLOCK_ID)->fetch();
        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();
        $result = $entityDataClass::getList($filter);
        if($result>0){
            return $result;
        }
        else{
            return false;
        }
    }

    public static function addLog($message,$ITEM_ID='',$severity='ERROR'){
        \CEventLog::Add(array(
            "SEVERITY" => $severity,
            "AUDIT_TYPE_ID" => "Renins",
            "MODULE_ID" => "Renins",
            "ITEM_ID" => $ITEM_ID,
            "DESCRIPTION" => $message,
        ));
    }

    /**
     * Возвращает количество заявок рекрутёра, которые не взяли в работу
     * @return int
     */
    public static function getCountRecruiterItems() {
        $ib = new \Renins\IB('recruitment_form');
        $ib->setFilterParam('PROPERTY_OTOBRAZHAT_ZAYAVKU_REKRUTERAM', 1);
        $ib->setFilterParam('PROPERTY_RECRUITER', false);
        $items = $ib->getList();
        return count($items);

    }

    /**
     * Возвращает количество массовых заявок рекрутёра, которые не взяли в работу
     * @return int
     */
    public static function getCountMassRecruiterItems() {
        $ib = new \Renins\IB('mass_recruitment_form');
        $ib->setFilterParam('PROPERTY_OTOBRAZHAT_ZAYAVKU_REKRUTERAM', 1);
        $ib->setFilterParam('PROPERTY_RECRUITER', false);
        $items = $ib->getList();
        return count($items);
    }

    /**
     * Возвращает массив id свойств по коду инфоблока
     *
     * @return array
     */
    public static function getPropertiesByIblockCode($code) {
        $iblock_id = \Renins\Helper::getIdbyCode($code);
        $properties = \CIBlock::GetProperties($iblock_id);
        $result = [];
        while($res_properties = $properties->Fetch()) {
            $result[$res_properties["CODE"]] = $res_properties["ID"];
        }
        return $result;
    }

    /**
     * Смена раскладки с латиницы на кириллицу
     *
     * @param $value
     * @return string
     */
    public static function switcher_ru($value)
    {
        $converter = array(
            'f' => 'а',	',' => 'б',	'd' => 'в',	'u' => 'г',	'l' => 'д',	't' => 'е',	'`' => 'ё',
            ';' => 'ж',	'p' => 'з',	'b' => 'и',	'q' => 'й',	'r' => 'к',	'k' => 'л',	'v' => 'м',
            'y' => 'н',	'j' => 'о',	'g' => 'п',	'h' => 'р',	'c' => 'с',	'n' => 'т',	'e' => 'у',
            'a' => 'ф',	'[' => 'х',	'w' => 'ц',	'x' => 'ч',	'i' => 'ш',	'o' => 'щ',	'm' => 'ь',
            's' => 'ы',	']' => 'ъ',	"'" => "э",	'.' => 'ю',	'z' => 'я',

            'F' => 'А',	'<' => 'Б',	'D' => 'В',	'U' => 'Г',	'L' => 'Д',	'T' => 'Е',	'~' => 'Ё',
            ':' => 'Ж',	'P' => 'З',	'B' => 'И',	'Q' => 'Й',	'R' => 'К',	'K' => 'Л',	'V' => 'М',
            'Y' => 'Н',	'J' => 'О',	'G' => 'П',	'H' => 'Р',	'C' => 'С',	'N' => 'Т',	'E' => 'У',
            'A' => 'Ф',	'{' => 'Х',	'W' => 'Ц',	'X' => 'Ч',	'I' => 'Ш',	'O' => 'Щ',	'M' => 'Ь',
            'S' => 'Ы',	'}' => 'Ъ',	'"' => 'Э',	'>' => 'Ю',	'Z' => 'Я',

            '@' => '"',	'#' => '№',	'$' => ';',	'^' => ':',	'&' => '?',	'/' => '.',	'?' => ',',
        );

        $value = strtr($value, $converter);
        return $value;
    }
}
