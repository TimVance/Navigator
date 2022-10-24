<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);
CJSCore::Init(array('jquery'));
global $APPLICATION, $right, $Apply, $REQUEST_METHOD, $RestoreDefaults;
$moduleId = 'renins';
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
        array(
            'DIV' => 'boss',
            'TAB' => "Интеграция с БОСС-кадровик",
            'ICON' => '',
            'TITLE' => "Настройки",
        ),array(
            'DIV' => 'heath',
            'TAB' => "Интеграция с AD",
            'ICON' => '',
            'TITLE' => "Настройки",
        ),array(
            'DIV' => 'jira',
            'TAB' => "Интеграция с Jira",
            'ICON' => '',
            'TITLE' => "Настройки",
        ),array(
            'DIV' => 'ib',
            'TAB' => "Интеграция с ИБ",
            'ICON' => '',
            'TITLE' => "Настройки",
        ),array(
            'DIV' => 'edit5',
            'TAB' => Loc::getMessage('MODULE_RIGHTS_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_RIGHTS_TAB_TITLE'),
        ),array(
            'DIV' => 'questionnaire90',
            'TAB' => Loc::getMessage('MODULE_QUESTIONNAIRE_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_QUESTIONNAIRE_TAB_TITLE'),
        ),array(
            'DIV' => 'questionnaireDMS',
            'TAB' => Loc::getMessage('MODULE_QUESTIONNAIRE_DMS_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_QUESTIONNAIRE_DMS_TAB_TITLE'),
        ),array(
            'DIV' => 'projects',
            'TAB' => Loc::getMessage('MODULE_PROJECTS_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_PROJECTS_TAB_TITLE'),
        ),array(
            'DIV' => 'formone',
            'TAB' => Loc::getMessage('MODULE_FORM_ONE_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_FORM_ONE_SETTINGS_TITLE'),
        ),array(
            'DIV' => 'xhprof',
            'TAB' => Loc::getMessage('MODULE_XHPROF_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_XHPROF_TITLE'),
        ),array(
            'DIV' => 'badges',
            'TAB' => Loc::getMessage('MODULE_BADGES_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_BADGES_TITLE'),
        ),
        array(
            'DIV' => 'timesheet',
            'TAB' => Loc::getMessage('MODULE_TIMESHEET_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_TIMESHEET_TITLE'),
        ),
        array(
            'DIV' => 'corpcrm',
            'TAB' => Loc::getMessage('MODULE_CORPCRM_TAB'),
            'ICON' => '',
            'TITLE' => Loc::getMessage('MODULE_CORPCRM_TITLE'),
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
            "BLOCK_EXT_LIST",
            "BOSS_PWD",
            "BOSS_SERVER_NAME",
            "BOSS_UID",
            "ID_PROJECT",
            "ID_USERS_LIST_ACCOMPLICES",
            "ID_USERS_LIST_LINK_REPORT",
            "ID_USERS_LIST_LINK_REPORT_DMS_TEST_RESULT",
            "ID_USERS_LIST_REMOVE_DMS_POLL",
            "ID_USER_RESPONSIBLE",
            "MAX_FILE_SIZE_USER_UPLOAD",
            "MAX_STORAGE_SIZE",
            "RS_DEPARTMENT_DEF_ID",
            "RS_NOT_DEPARTMENT_DEF_ID",
            "CLINIC_AD_CONNECTOR_ID",
            "CLINIC_DEPARTMENT_DEF_ID",
            "HEALTH_AD_CONNECTOR_ID",
            "HEALTH_DEPARTMENT_DEF_ID",
            "JIRA_LOGIN",
            "JIRA_PASS",
            "IB_LOGIN",
            "IB_PASS",
            "BOSS_VACATION_HOLIDAYS_TABLE",
            "BOSS_VACATION_LEAVE_DAYS_TABLE",
            "BOSS_VACATION_TABLE",
            "BOSS_VACATION_FUNC_CITIES",
            "BOSS_ORGANIZATIONAL_STRUCTURE_TABLE",
            "RS_DEPARTMENT_MEDKORP_ID",
            "RS_MEDKORP_UF_COMPANY_NAME",
            "RS_MEDKORP_MANAGER_ID",
            "ADD_FILE_DESCRIPTION",
            "MP4_MAX_WIDTH",
            "MP4_MAX_HEIGHT",
            "MP4_MAX_FILESIZE",
            "RS_DEPARTMENT_IT_GROUP_ID",
            "RS_DEPARTMENT_IT_OUTSOURSE_ID",
            "RS_DEPARTMENT_AGENTS_ID",
            "RS_HOLIDAYS",
            "RS_WEEKEND",
            "RS_WORK_DAYS",
            "FORM_ONE_SETTINGS_HOST",
            "FORM_ONE_SETTINGS_LOGIN",
            "FORM_ONE_SETTINGS_PASSWORD",
            "FORM_ONE_SETTINGS_DIALOG_ID",
            "FORM_ONE_SETTINGS_VERSION_API",
            "DMS_TEST_ENABLED",
            "XHPROF_DIVIDER",
            "XHPROF_ADDRESS",
            "XHPROF_USER",
            "XHPROF_PASSWORD",
            "BOSS_CITIES_TABLE",
            "BUDU_AD_CONNECTOR_ID",
            "BUDU_DEPARTMENT_DEF_ID",
            "LDAP_TO_SOCGROUP",
            "RENINS_NEW_YEAR_THEME",
            "RENINS_SPRING_THEME",
            "RENINS_CUSTOM_THEME",
            "BADGES_SHOW",
            "IS_TEST",
            "BOSS_TB_RT_TYPES_TABLE",
            "BOSS_TB_TABLE",
            "BOSS_COST_CENTER_TABLE",
            "BOSS_TB_COST_TOPIC",
            "BOSS_DISMISSED_TABLE",
            "RENINS_TIMESHEET_SHOW_LINK",
            "RENINS_TIMESHEET_SHOW_ADMIN_LINK",
            "MODULE_TIMESHEET_PERIOD_SEPARATOR",
            "RENINS_TIMESHEET_EXCLUDE_COMPANY_EXP",
            "RENINS_CORP_CRM_TOKEN",
            "RENINS_TIMESHEET_OVERWORK_BUTTON"
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
    ?>
    <form method='post' name='<?= $moduleId ?>_opt_form'
          action='<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($moduleId) ?>&amp;lang=<?= LANGUAGE_ID ?>'>

        <?php
        $tabControl->Begin();
        $tabControl->BeginNextTab();?>
        <? // region Настройки ?>
        <tr>
            <td>Максимальный размер файла для загрузки на портал пользователем</td>
            <td><input type="text" name="MAX_FILE_SIZE_USER_UPLOAD" value="<?= COption::GetOptionString($moduleId, "MAX_FILE_SIZE_USER_UPLOAD") ?>" size="20"> байт</td>
        </tr>
        <tr>
            <td>Суммарный размер файлов для загрузки на портал пользователем</td>
            <td><input type="text" name="MAX_STORAGE_SIZE" value="<?= COption::GetOptionString($moduleId, "MAX_STORAGE_SIZE") ?>" size="20"> байт</td>
        </tr>
        <tr>
            <td>Список запрещенных расширений файлов</td>
            <td><textarea type="text" name="BLOCK_EXT_LIST" value="" cols="60" rows="5"><?= COption::GetOptionString($moduleId, "BLOCK_EXT_LIST") ?></textarea></td>
        </tr>
        <tr>
            <td>Текстовая подсказка при добавлении файла</td>
            <td><textarea type="text" name="ADD_FILE_DESCRIPTION" value="" cols="60" rows="5"><?= COption::GetOptionString($moduleId, "ADD_FILE_DESCRIPTION") ?></textarea></td>
        </tr>
        <tr>
            <td>Максимальная ширина mp4</td>
            <td><input type="text" name="MP4_MAX_WIDTH" value="<?= COption::GetOptionString($moduleId, "MP4_MAX_WIDTH") ?>" size="20"> px</td>
        </tr>
        <tr>
            <td>Максимальная высота mp4</td>
            <td><input type="text" name="MP4_MAX_HEIGHT" value="<?= COption::GetOptionString($moduleId, "MP4_MAX_HEIGHT") ?>" size="20"> px</td>
        </tr>
        <tr>
            <td>Максимальная размер mp4 в байтах</td>
            <td><input type="text" name="MP4_MAX_FILESIZE" value="<?= COption::GetOptionString($moduleId, "MP4_MAX_FILESIZE") ?>" size="20"> байт</td>
        </tr>
        <tr>
            <td>Тестовая среда</td>
            <td>
                <input type="hidden" name="IS_TEST" value="N">
                <input type="checkbox" name="IS_TEST" value="Y"
                    <?= COption::GetOptionString($moduleId, "IS_TEST") == 'Y' ? 'checked' : '' ?>
                />
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">Настройка соц. групп для коннекторов</td>
        </tr>
        <?
        $rsLdapServers = \CLdapServer::GetList([], ['ACTIVE'=>'Y']);
        while ($arLdapServer = $rsLdapServers->Fetch())
        {
            ?>
            <tr>
                <td><?=$arLdapServer['NAME'];?>[<?=$arLdapServer['ID'];?>] ID группы:</td>
                <td><input type="text" name="LDAP_TO_SOCGROUP[<?=$arLdapServer['ID']?>]" value="<?= COption::GetOptionString($moduleId, "LDAP_TO_SOCGROUP_" . $arLdapServer['ID']) ?>" size="20"></td>
            </tr>
            <?
        }
        ?>
        <tr>
            <td>Новогодняя тема</td>
            <td>
                <input type="hidden" name="RENINS_NEW_YEAR_THEME" value="N">
                <input type="checkbox" name="RENINS_NEW_YEAR_THEME" value="Y"
                    <?= COption::GetOptionString($moduleId, "RENINS_NEW_YEAR_THEME") == 'Y' ? 'checked' : '' ?>
                />
            </td>
        </tr>
        <tr>
            <td>Весенняя тема</td>
            <td>
                <input type="hidden" name="RENINS_SPRING_THEME" value="N">
                <input type="checkbox" name="RENINS_SPRING_THEME" value="Y"
                    <?= COption::GetOptionString($moduleId, "RENINS_SPRING_THEME") == 'Y' ? 'checked' : '' ?>
                />
            </td>
        </tr>
        <tr>
            <td>Кастомная тема (файл фона <a href="/upload/custom_theme/custom_bg.svg" target="_blank">/upload/custom_theme/custom_bg.svg</a>)</td>
            <td>
                <input type="hidden" name="RENINS_CUSTOM_THEME" value="N">
                <input type="checkbox" name="RENINS_CUSTOM_THEME" value="Y"
                    <?= COption::GetOptionString($moduleId, "RENINS_CUSTOM_THEME") == 'Y' ? 'checked' : '' ?>
                />
            </td>
        </tr>

        <? // endregion ?>
        <? //region Интеграция с БОСС-каровиком ?>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td>SERVER_NAME</td>
            <td><input type="text" name="BOSS_SERVER_NAME" value="<?= COption::GetOptionString($moduleId, "BOSS_SERVER_NAME") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>UID</td>
            <td><input type="text" name="BOSS_UID" value="<?= COption::GetOptionString($moduleId, "BOSS_UID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>PWD</td>
            <td><input type="password" name="BOSS_PWD" value="<?= COption::GetOptionString($moduleId, "BOSS_PWD") ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Таблица списка выходных и праздничных дней в MsSql</td>
            <td><input type="text" name="BOSS_VACATION_HOLIDAYS_TABLE" value="<?= COption::GetOptionString($moduleId, "BOSS_VACATION_HOLIDAYS_TABLE") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>Таблица рассчитанных остатков в MsSql</td>
            <td><input type="text" name="BOSS_VACATION_LEAVE_DAYS_TABLE" value="<?= COption::GetOptionString($moduleId, "BOSS_VACATION_LEAVE_DAYS_TABLE") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>Таблица запланированных отпусков в MsSql</td>
            <td><input type="text" name="BOSS_VACATION_TABLE" value="<?= COption::GetOptionString($moduleId, "BOSS_VACATION_TABLE") ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Перечень городов, где согласование отпуска делает функциональный руководитель (через ;)</td>
            <td><input type="text" name="BOSS_VACATION_FUNC_CITIES" value="<?= COption::GetOptionString($moduleId, "BOSS_VACATION_FUNC_CITIES") ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Таблица с оргструктурой в MsSql</td>
            <td><input type="text" name="BOSS_ORGANIZATIONAL_STRUCTURE_TABLE" value="<?= COption::GetOptionString($moduleId, "BOSS_ORGANIZATIONAL_STRUCTURE_TABLE") ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Таблица с городами в MsSql</td>
            <td><input type="text" name="BOSS_CITIES_TABLE" value="<?= COption::GetOptionString($moduleId, "BOSS_CITIES_TABLE") ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Таблица с табелем учета рабочего времени в MsSql</td>
            <td><input type="text" name="BOSS_TB_TABLE"
                       value="<?= COption::GetOptionString($moduleId, "BOSS_TB_TABLE") ?>" size="40"></td>
        </tr>
        <tr>
            <td>Таблица cost-center в MsSql</td>
            <td><input type="text" name="BOSS_COST_CENTER_TABLE"
                       value="<?= COption::GetOptionString($moduleId, "BOSS_COST_CENTER_TABLE") ?>" size="40"></td>
        </tr>

        <tr>
            <td>Таблица с типами для табеля учета рабочего времени в MsSql</td>
            <td><input type="text" name="BOSS_TB_RT_TYPES_TABLE"
                       value="<?= COption::GetOptionString($moduleId, "BOSS_TB_RT_TYPES_TABLE") ?>" size="40"></td>
        </tr>

        <tr>
            <td>Таблица с темами затрат и номерами заявок на подбор в MsSql</td>
            <td><input type="text" name="BOSS_TB_COST_TOPIC"
                       value="<?= COption::GetOptionString($moduleId, "BOSS_TB_COST_TOPIC") ?>" size="40"></td>
        </tr>

        <tr>
            <td>Таблица с уволенными сотрудниками</td>
            <td><input type="text" name="BOSS_DISMISSED_TABLE"
                       value="<?= COption::GetOptionString($moduleId, "BOSS_DISMISSED_TABLE") ?>" size="40"></td>
        </tr>

        <tr>
            <td>ID корневого подразделения Ренесанс Страхования</td>
            <td><input type="text" name="RS_DEPARTMENT_DEF_ID" value="<?= COption::GetOptionString($moduleId, "RS_DEPARTMENT_DEF_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID подразделения Ренесанс Страхования для сотрудников без подразделения</td>
            <td><input type="text" name="RS_NOT_DEPARTMENT_DEF_ID" value="<?= COption::GetOptionString($moduleId, "RS_NOT_DEPARTMENT_DEF_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID подразделения "Группа здоровье" для перемещения в нее Медкорп</td>
            <td><input type="text" name="RS_DEPARTMENT_MEDKORP_ID" value="<?= COption::GetOptionString($moduleId, "RS_DEPARTMENT_MEDKORP_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID руководителя Медкорп, который будет перемещен в подразделение "Группа здоровье"</td>
            <td><input type="text" name="RS_MEDKORP_MANAGER_ID" value="<?= htmlspecialchars(COption::GetOptionString($moduleId, "RS_MEDKORP_MANAGER_ID")) ?>" size="40"> </td>
        </tr>
        <!--<tr>
            <td>Значение поля UF_COMPANY_NAME у сотрудников Медкорп</td>
            <td><input type="text" name="RS_MEDKORP_UF_COMPANY_NAME" value="<?/*= htmlspecialchars(COption::GetOptionString($moduleId, "RS_MEDKORP_UF_COMPANY_NAME")) */?>" size="40"> </td>
        </tr>-->
        <tr>
            <td>ID группы ИТ-аутсорс</td>
            <td><input type="text" name="RS_DEPARTMENT_IT_GROUP_ID" value="<?= COption::GetOptionString($moduleId, "RS_DEPARTMENT_IT_GROUP_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID подразделения для ИТ-аутсорс</td>
            <td><input type="text" name="RS_DEPARTMENT_IT_OUTSOURSE_ID" value="<?= htmlspecialchars(COption::GetOptionString($moduleId, "RS_DEPARTMENT_IT_OUTSOURSE_ID")) ?>" size="40"> </td>
        </tr>

        <tr>
            <td>ID подразделения для Агенты</td>
            <td><input type="text" name="RS_DEPARTMENT_AGENTS_ID" value="<?= htmlspecialchars(COption::GetOptionString($moduleId, "RS_DEPARTMENT_AGENTS_ID")) ?>" size="40"> </td>
        </tr>


        <tr>
            <td>Перечень праздничных дней через ; пример: 01.01;02.01;03.01</td>
            <td><input type="text" name="RS_HOLIDAYS" value="<?= htmlspecialchars(COption::GetOptionString($moduleId, "RS_HOLIDAYS")) ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Перечень выходных дней(перенесенных) через ; пример: 10.05;14.06</td>
            <td><input type="text" name="RS_WEEKEND" value="<?= htmlspecialchars(COption::GetOptionString($moduleId, "RS_WEEKEND")) ?>" size="40"> </td>
        </tr>

        <tr>
            <td>Перечень рабочих дней через ; пример: 20.03</td>
            <td><input type="text" name="RS_WORK_DAYS" value="<?= htmlspecialchars(COption::GetOptionString($moduleId, "RS_WORK_DAYS")) ?>" size="40"> </td>
        </tr>
        <? // endregion ?>
        <? //region Интеграция с AD ?>
        <?php


        $tabControl->BeginNextTab();
        ?>
        <tr class="heading">
            <td colspan="2">Здоровье</td>
        </tr>
        <tr>
            <td>ID коннектора здоровья</td>
            <td><input type="text" name="HEALTH_AD_CONNECTOR_ID" value="<?= COption::GetOptionString($moduleId, "HEALTH_AD_CONNECTOR_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID корневого подразделения Ренесанс Здоровье</td>
            <td><input type="text" name="HEALTH_DEPARTMENT_DEF_ID" value="<?= COption::GetOptionString($moduleId, "HEALTH_DEPARTMENT_DEF_ID") ?>" size="40"> </td>
        </tr>
        <tr class="heading">
            <td colspan="2">Клиники</td>
        </tr>
        <tr>
            <td>ID коннектора клиник</td>
            <td><input type="text" name="CLINIC_AD_CONNECTOR_ID" value="<?= COption::GetOptionString($moduleId, "CLINIC_AD_CONNECTOR_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID корневого подразделения Ренесанс Клиники</td>
            <td><input type="text" name="CLINIC_DEPARTMENT_DEF_ID" value="<?= COption::GetOptionString($moduleId, "CLINIC_DEPARTMENT_DEF_ID") ?>" size="40"> </td>

        <tr class="heading">
            <td colspan="2">Budu</td>
        </tr>

        <tr>
            <td>ID коннектора Budu</td>
            <td><input type="text" name="BUDU_AD_CONNECTOR_ID" value="<?= COption::GetOptionString($moduleId, "BUDU_AD_CONNECTOR_ID") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>ID корневого подразделения Budu</td>
            <td><input type="text" name="BUDU_DEPARTMENT_DEF_ID" value="<?= COption::GetOptionString($moduleId, "BUDU_DEPARTMENT_DEF_ID") ?>" size="40"> </td>
        </tr>
        <!--       <tr>
            <td>ID подразделения Ренесанс Здоровье для сотрудников без подразделения</td>
            <td><input type="text" name="RS_NOT_DEPARTMENT_DEF_ID" value="<?/*= COption::GetOptionString($moduleId, "HEALTH_NOT_DEPARTMENT_DEF_ID") */?>" size="40"> </td>
        </tr>-->
        <?//endregion?>
        <? //region Интеграция с Jira ?>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td>Логин</td>
            <td><input type="text" name="JIRA_LOGIN" value="<?= COption::GetOptionString($moduleId, "JIRA_LOGIN") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>Пароль</td>
            <td><input type="password" name="JIRA_PASS" value="<?= COption::GetOptionString($moduleId, "JIRA_PASS") ?>" size="40"> </td>
        </tr>
        <?//endregion?>
        <? //region Интеграция с ИБ ?>
        <?php
        $tabControl->BeginNextTab();
        ?>
        <tr>
            <td>Логин</td>
            <td><input type="text" name="IB_LOGIN" value="<?= COption::GetOptionString($moduleId, "IB_LOGIN") ?>" size="40"> </td>
        </tr>
        <tr>
            <td>Пароль</td>
            <td><input type="password" name="IB_PASS" value="<?= COption::GetOptionString($moduleId, "IB_PASS") ?>" size="40"> </td>
        </tr>
        <? //endregion ?>
        <? //region Права на модуль ?>
        <?php
        $tabControl->BeginNextTab();
        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');
        ?>
        <?//endregion?>
        <? //region Опросник90 ?>
        <?
        $tabControl->BeginNextTab(); // Вкладка опросник questionnaire90
        ?>
        <tr>
            <td>ID проекта</td>
            <td><input type="text" name="ID_PROJECT"
                       value="<?= COption::GetOptionString($moduleId, "ID_PROJECT") ?>" size="5"></td>
        </tr>
        <tr>
            <td>ID ответственного</td>
            <td><input type="text" name="ID_USER_RESPONSIBLE"
                       value="<?= COption::GetOptionString($moduleId, "ID_USER_RESPONSIBLE") ?>" size="5"></td>
        </tr>
        <tr>
            <td>ID Соисполнителей (через ;)</td>
            <td><input type="text" name="ID_USERS_LIST_ACCOMPLICES"
                       value="<?= COption::GetOptionString($moduleId, "ID_USERS_LIST_ACCOMPLICES") ?>" size="90"></td>
        </tr>
        <tr>
            <td>Отображать в виджете ссылку на отчет пользователям (ID через ;)</td>
            <td><input type="text" name="ID_USERS_LIST_LINK_REPORT"
                       value="<?= COption::GetOptionString($moduleId, "ID_USERS_LIST_LINK_REPORT") ?>" size="90"></td>
        </tr>
        <? //endregion ?>
        <? //region Опросник ДМС ?>
        <?
        $tabControl->BeginNextTab(); // Вкладка опросник questionnaireDMS
        ?>
        <tr>
            <td>Модуль активен</td>
            <td>
                <input type="hidden" name="DMS_TEST_ENABLED" value="N">
                <input type="checkbox" name="DMS_TEST_ENABLED" value="Y" <?= COption::GetOptionString($moduleId, "DMS_TEST_ENABLED") == 'Y' ? 'checked' : '' ?>></td>
        </tr>
        <tr>
            <td>Отображать в виджете ссылку на отчет пользователям (ID через ;)</td>
            <td><input type="text" name="ID_USERS_LIST_LINK_REPORT_DMS_TEST_RESULT"
                       value="<?= COption::GetOptionString($moduleId, "ID_USERS_LIST_LINK_REPORT_DMS_TEST_RESULT") ?>" size="90"></td>
        </tr>
        <tr>
            <td>Исключить пользователей из опроса(ID через ;)</td>
            <td><input type="text" name="ID_USERS_LIST_REMOVE_DMS_POLL"
                       value="<?= COption::GetOptionString($moduleId, "ID_USERS_LIST_REMOVE_DMS_POLL") ?>" size="90"></td>
        </tr>
        <? //endregion ?>
        <? //region Проекты ?>
        <?
        $tabControl->BeginNextTab(); // Вкладка проекты
        if($_REQUEST['project_msg']) {
            ?>
            <tr>
                <td style="color: red; font-weight: bold"><?= $_REQUEST['project_msg']; ?></td>
            </tr>
            <?
        }
        ?>
        <tr>
            <td colspan="2">Данный функционал удалит сотрудника из всех проектов и добавит вместо него другого сотрудника (в качестве участника проекта)</td>
        </tr>
        <tr>
            <td>ID сотрудника, которого нужно удалить из проектов</td>
            <td><input type="text" name="PROJECTS_OWNER_ID"
                       value="<?= COption::GetOptionString($moduleId, "PROJECTS_OWNER_ID") ?>" size="90"></td>
        </tr>
        <tr>
            <td>ID сотрудника когорого нужно добавить в проекты (вместо предыдущего)</td>
            <td><input type="text" name="PROJECTS_NEW_OWNER_ID"
                       value="<?= COption::GetOptionString($moduleId, "PROJECTS_NEW_OWNER_ID") ?>" size="90"></td>
        </tr>
        <? //endregion?>
        <? //region Connection FormOne?>
        <? $tabControl->BeginNextTab(); ?>
        <tr>
            <td colspan="2"><?= Loc::getMessage("MODULE_FORM_ONE_SETTINGS_TITLE")?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_FORM_ONE_SETTINGS_HOST")?></td>
            <td><input type="text" name="FORM_ONE_SETTINGS_HOST"
                       value="<?= Option::get($moduleId, "FORM_ONE_SETTINGS_HOST") ?>" size="90"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_FORM_ONE_SETTINGS_VERSION_API")?></td>
            <td><input type="text" name="FORM_ONE_SETTINGS_VERSION_API"
                       value="<?= Option::get($moduleId, "FORM_ONE_SETTINGS_VERSION_API") ?>" size="90"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_FORM_ONE_SETTINGS_DIALOG_ID")?></td>
            <td><input type="number" name="FORM_ONE_SETTINGS_DIALOG_ID"
                       value="<?= Option::get($moduleId, "FORM_ONE_SETTINGS_DIALOG_ID") ?>" size="90"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_FORM_ONE_SETTINGS_LOGIN")?></td>
            <td><input type="text" name="FORM_ONE_SETTINGS_LOGIN"
                       value="<?= Option::get($moduleId, "FORM_ONE_SETTINGS_LOGIN") ?>" size="90"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_FORM_ONE_SETTINGS_PASSWORD")?></td>
            <td><input type="password" name="FORM_ONE_SETTINGS_PASSWORD"
                       value="<?= Option::get($moduleId, "FORM_ONE_SETTINGS_PASSWORD") ?>" size="90"></td>
        </tr>
        <? //endregion?>
        <? //region Profiler?>
        <? $tabControl->BeginNextTab(); ?>
        <tr>
            <td colspan="2"><?= Loc::getMessage("MODULE_XHPROF_TITLE")?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_XHPROF_DIVIDER")?></td>
            <td><input type="number" name="XHPROF_DIVIDER"
                       value="<?= Option::get($moduleId, "XHPROF_DIVIDER") ?>" size="20"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_XHPROF_ADDRESS")?></td>
            <td><input type="text" name="XHPROF_ADDRESS"
                       value="<?= Option::get($moduleId, "XHPROF_ADDRESS") ?>" size="20"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_XHPROF_USER")?></td>
            <td><input type="text" name="XHPROF_USER"
                       value="<?= Option::get($moduleId, "XHPROF_USER") ?>" size="20"></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_XHPROF_PASSWORD")?></td>
            <td><input type="password" name="XHPROF_PASSWORD"
                       value="<?= Option::get($moduleId, "XHPROF_PASSWORD") ?>" size="20"></td>
        </tr>
        <? //endregion?>
	    <? //region Badges?>
        <? $tabControl->BeginNextTab(); ?>
        <tr>
            <td><?= Loc::getMessage("MODULE_BADGES_SHOW")?></td>
            <td>
                <select name="BADGES_SHOW">
                    <option <? if(Option::get($moduleId, "BADGES_SHOW") == 'admin') echo 'selected'; ?> value="admin">Только администраторам</option>
                    <option <? if(Option::get($moduleId, "BADGES_SHOW") == 'all') echo 'selected'; ?> value="all">Всем пользователям</option>
                </select>
            </td>
        </tr>
        <? //endregion?>
	    <? //region Timesheet?>
	    <? $tabControl->BeginNextTab(); ?>
        <tr>
            <td><?= Loc::getMessage("MODULE_TIMESHEET_SHOW_LINK")?></td>
            <td>
                <input type="hidden" name="RENINS_TIMESHEET_SHOW_LINK" value="N">
                <input type="checkbox" name="RENINS_TIMESHEET_SHOW_LINK" value="Y" <?= COption::GetOptionString($moduleId, "RENINS_TIMESHEET_SHOW_LINK") == 'Y' ? 'checked' : '' ?>>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("RENINS_TIMESHEET_OVERWORK_BUTTON")?></td>
            <td>
                <input type="hidden" name="RENINS_TIMESHEET_OVERWORK_BUTTON" value="N">
                <input type="checkbox" name="RENINS_TIMESHEET_OVERWORK_BUTTON" value="Y" <?= COption::GetOptionString($moduleId, "RENINS_TIMESHEET_OVERWORK_BUTTON") == 'Y' ? 'checked' : '' ?>>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_TIMESHEET_SHOW_ADMIN_LINK")?></td>
            <td>
                <input type="hidden" name="RENINS_TIMESHEET_SHOW_ADMIN_LINK" value="N">
                <input type="checkbox" name="RENINS_TIMESHEET_SHOW_ADMIN_LINK" value="Y" <?= COption::GetOptionString($moduleId, "RENINS_TIMESHEET_SHOW_ADMIN_LINK") == 'Y' ? 'checked' : '' ?>>
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_TIMESHEET_PERIOD_SEPARATOR")?></td>
            <td>
                <input type="text" name="RENINS_TIMESHEET_PERIOD_SEPARATOR" value="<?= COption::GetOptionString($moduleId, "RENINS_TIMESHEET_PERIOD_SEPARATOR") ?>" size="20">
            </td>
        </tr>
        <tr>
            <td><?= Loc::getMessage("MODULE_TIMESHEET_EXCLUDE_COMPANY")?></td>
            <td>
                <input type="text" name="RENINS_TIMESHEET_EXCLUDE_COMPANY_EXP" value="<?= COption::GetOptionString($moduleId, "RENINS_TIMESHEET_EXCLUDE_COMPANY_EXP") ?>" size="20">
            </td>
        </tr>
        <? //endregion?>
        <? $tabControl->BeginNextTab(); ?>
        <tr>
            <td><?= Loc::getMessage("MODULE_CORP_CRM_TOKEN")?></td>
            <td><input type="text" name="RENINS_CORP_CRM_TOKEN" value="<?= COption::GetOptionString($moduleId, "RENINS_CORP_CRM_TOKEN") ?>" size="20"></td>
        </tr>
        <? //endregion?>
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
