<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Context;
use Bitrix\Main\
{Loader, Application, Data\Cache, EventManager};

if ($_REQUEST["recalc"] == "Y") {

    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
    define("STOP_STATISTICS", true);
    $APPLICATION->RestartBuffer();
    $request = Context::getCurrent()->getRequest();
    Loader::IncludeModule('gmdiscounts');
    echo \GM\Discounts::calcDiscounts();
    exit();
}

Loc::loadMessages(__FILE__);
CJSCore::Init(array('jquery'));
global $APPLICATION, $right, $Apply, $REQUEST_METHOD, $RestoreDefaults;
$moduleId = 'gmdiscounts';
$module_id = $moduleId;
$right = $APPLICATION->GetGroupRight($moduleId);
if ($right >= 'R') {
    Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
    $generalOptions = array();
    $allOptions = array(
        $generalOptions,
    );
    $tabs = array(
        array(
            'DIV' => 'edit',
            'TAB' => "Настройки",
            'ICON' => '',
            'TITLE' => "Настройки",
        ),
    );
    $tabControl = new CAdminTabControl('tabControl', $tabs);
    CModule::IncludeModule($moduleId);
    if (($REQUEST_METHOD == 'POST') && (strlen($Update . $Apply . $RestoreDefaults) > 0)
        && $right == 'W' && check_bitrix_sessid()
    ) {
        if (strlen($RestoreDefaults) > 0) {
            COption::RemoveOption($moduleId);
        } else {
            foreach ($allOptions as $optionsSection) {
                foreach ($optionsSection as $option) {
                    $name = $option['ID'];
                    $value = trim($_REQUEST[$name]);
                    if ($option['TYPE'] == 'checkbox' && $value != 'Y') {
                        $value = 'N';
                    }
                    COption::SetOptionString($moduleId, $name, $value, $option['MESSAGE']);
                }
            }
        }
        $settings = [
            "iblock",
            "price1",
            "price2",
            "price1",
            "write",
            "autocalc",
            "step"
        ];
        foreach ($settings as $key) {
            if (is_array($_REQUEST[$key])){
                // чтобы галочки очищались у чекбоксов - перед инпутом в верстке надо ставить hidden с value="N"
                foreach ($_REQUEST[$key] as $kkey => $value){
                    COption::SetOptionString($moduleId, $key . '_' . $kkey, $value, false);
                }
            }  else {
                COption::SetOptionString($moduleId, $key, $_REQUEST[$key]);
            }
        }
        $dop_params = '';
        if ($_REQUEST['PROJECTS_OWNER_ID'] && $_REQUEST['PROJECTS_NEW_OWNER_ID'] && $tabControl->selectedTab == 'projects') {
            $project_msg = \Renins\Project::changeAllProjectsUser($_REQUEST['PROJECTS_OWNER_ID'], $_REQUEST['PROJECTS_NEW_OWNER_ID']);
            $dop_params .= '&project_msg=' . $project_msg;
        }

        ob_start();
        $Update = $Update . $Apply;

        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');
        ob_end_clean();
        if (strlen($_REQUEST['back_url_settings']) > 0) {
            if ((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0)) {
                LocalRedirect(
                    $APPLICATION->GetCurPage() . '?mid=' . urlencode($moduleId) . '&lang=' . urlencode(LANGUAGE_ID)
                    . '&back_url_settings=' . urlencode($_REQUEST['back_url_settings']) . '&'
                    . $tabControl->ActiveTabParam() . $dop_params
                );
            } else {
                LocalRedirect($_REQUEST['back_url_settings'] . $dop_params);
            }
        } else {
            LocalRedirect(
                $APPLICATION->GetCurPage() . '?mid=' . urlencode($moduleId) . '&lang=' . urlencode(LANGUAGE_ID)
                . '&' . $tabControl->ActiveTabParam() . $dop_params
            );
        }
    }

    /* Список инфоблоков */
    $res = CIBlock::GetList(
        Array(),
        Array(
            'ACTIVE'=>'Y',
            "CNT_ACTIVE"=>"Y",
        ), true
    );
    $iblock_list = [];
    while($ar_res = $res->Fetch())
    {
        $iblock_list[$ar_res["ID"]] = $ar_res;
    }


    /* Список цен */
    $dbPriceType = CCatalogGroup::GetList(
        array("SORT" => "ASC"),
        array()
    );
    $price_types = [];
    while ($arPriceType = $dbPriceType->Fetch())
    {
        $price_types[$arPriceType["ID"]] = $arPriceType;
    }


    /* Список свойств инфоблока */
    $field_list = [];
    if (!empty(COption::GetOptionString($moduleId, "iblock"))) {
        $properties = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => COption::GetOptionString($moduleId, "iblock")));
        while ($prop_fields = $properties->GetNext()) {
            $field_list[$prop_fields["ID"]] = $prop_fields;
        }
    } else {
        COption::SetOptionString($moduleId, "write", "");
    }

    ?>
    <form method='post' name='<?= $moduleId ?>_opt_form'
          action='<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($moduleId) ?>&amp;lang=<?= LANGUAGE_ID ?>'>

        <?php

        $tabControl->Begin();
        $tabControl->BeginNextTab();?>
        <? // region Настройки ?>
        <tr>
            <td style="width: 40%">Выбрать инфоблок</td>
            <td style="width: 60%;">
                <select class="changeSettings" name="iblock">
                    <option value="">-</option>
                    <? foreach ($iblock_list as $iblock_item) {
                        echo '<option '.(COption::GetOptionString($moduleId, "iblock") == $iblock_item["ID"] ? 'selected' : '').' value="'.$iblock_item["ID"].'">'.$iblock_item["NAME"].'('.$iblock_item["ID"].')</option>';
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 40%">Цена 1</td>
            <td style="width: 60%;">
                <select name="price1">
                    <option value="">-</option>
                    <? foreach ($price_types as $price_type) {
                        echo '<option '.(COption::GetOptionString($moduleId, "price1") == $price_type["ID"] ? 'selected' : '').' value="'.$price_type["ID"].'">'.$price_type["NAME"].'('.$price_type["ID"].')</option>';
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 40%">Цена 2</td>
            <td style="width: 60%;">
                <select name="price2">
                    <option value="">-</option>
                    <? foreach ($price_types as $price_type) {
                        echo '<option '.(COption::GetOptionString($moduleId, "price2") == $price_type["ID"] ? 'selected' : '').' value="'.$price_type["ID"].'">'.$price_type["NAME"].'('.$price_type["ID"].')</option>';
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 40%">Куда записываем</td>
            <td style="width: 60%;">
                <select name="write">
                    <option value="">-</option>
                    <? foreach ($field_list as $field_item) {
                        echo '<option '.(COption::GetOptionString($moduleId, "write") == $field_item["CODE"] ? 'selected' : '').' value="'.$field_item["CODE"].'">'.$field_item["NAME"].'('.$field_item["ID"].')</option>';
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td style="width: 40%">Шаг</td>
            <td style="width: 60%;">
                <input type="text" name="step" value="<?= !empty(COption::GetOptionString($moduleId, "step")) ? COption::GetOptionString($moduleId, "step") : 100 ?>">
            </td>
        </tr>
        <? if (COption::GetOptionString($moduleId, "write")): ?>
            <tr>
                <td style="width: 40%">Автоматический пересчет скидки при изменении элементов</td>
                <td style="width: 60%;">
                    <input type="checkbox" name="autocalc" value="Y" <? if (COption::GetOptionString($moduleId, "autocalc") == "Y") echo 'checked'; ?>> Да
                </td>
            </tr>
        <? endif; ?>
        <? if (COption::GetOptionString($moduleId, "write")): ?>
            <tr>
                <td style="width: 40%">Пересчет всех скидок</td>
                <td style="width: 60%;" class="progress-js">
                    <div class="recalc">Запустить</div>
                </td>
            </tr>
        <? endif; ?>
        <?
        $tabControl->Buttons();
        $disabled = ($right < 'W') ? 'disabled' : '';
        ?>
        <input <?= $disabled ?> type='submit' name='Update' value='<?= Loc::getMessage('MAIN_SAVE') ?>'
                                title='<?= Loc::getMessage('MAIN_OPT_SAVE_TITLE') ?>' class='adm-btn-save'
        />
        <input <?= $disabled ?> type='submit' name='Apply' value='<?= Loc::getMessage('MAIN_OPT_APPLY') ?>'
                                title='<?= Loc::getMessage('MAIN_OPT_APPLY_TITLE') ?>'
        />
        <?php if (strlen($_REQUEST['back_url_settings']) > 0): ?>
            <input <?= $disabled ?> type='button' name='Cancel' value='<?= Loc::getMessage('MAIN_OPT_CANCEL') ?>'
                                    title='<?= Loc::getMessage('MAIN_OPT_CANCEL_TITLE') ?>'
                                    onclick="window.location='<?= htmlspecialcharsbx(CUtil::addslashes($_REQUEST['back_url_settings'])) ?>"
            />
            <input type='hidden' name='back_url_settings'
                   value='<?= htmlspecialcharsbx($_REQUEST['back_url_settings']) ?>'
            />
        <?php endif ?>
        <input type='submit' name='RestoreDefaults' title='<?= Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS') ?>'
               OnClick="confirm('<?= AddSlashes(Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING')) ?>')"
               value='<?= Loc::getMessage('MAIN_RESTORE_DEFAULTS') ?>'
        />
        <?php
        echo bitrix_sessid_post();
        $tabControl->End();
        ?>
    </form>

    <?php
    if (!empty($notes)) {
        echo BeginNote();
        foreach ($notes as $key => $str) {
            echo '<span class="required"><sup>' . ($key + 1) . '</sup></span>' . $str . '<br>';
        }
        echo EndNote();
    }
}
?>

<script>
    $(function () {
        $(".changeSettings").change(function () {
            $("input[name='Apply']").click();
        });
        function sendRequest(data) {
            let info = data;
            $.ajax({
                url: '/bitrix/admin/settings.php?mid=gmdiscounts&lang=ru&recalc=Y',
                method: 'get',
                dataType: 'json',
                data: data,
                success: function(data) {
                    console.log(data);
                    if (data.last == '0') {
                        $(".progress-js").text('Расчет завершен. Всего обработано: ' + data.cnt);
                    } else {
                        $(".progress-js").text('Обработка ' + data.progress_cnt + ' из ' + data.cnt);
                        let result = {
                            action: 'process',
                            lastId: data.last,
                            cnt: data.cnt,
                            step_number: data.step_number,
                            progress_cnt: data.progress_cnt
                        };
                        window.setTimeout(sendRequest, 1000, result);
                    }
                },
                error: function (jqXHR, exception) {
                    alert('Ошибка!');
                    console.log(jqXHR, exception);
                }
            });
        }
        $(".recalc").click(function () {
            let data = {
                action: 'start',
                lastId: 0,
                cnt: 0
            };
            $(".progress-js").text('Запуск...');
            sendRequest(data);
        });
    });
</script>
<style>
    .recalc {
        display: inline-block;
        padding: 5px 10px;
        background-color: #b8cc5f;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
