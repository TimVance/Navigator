<?php


// Подгрузка классов модуля
Bitrix\Main\Loader::registerAutoLoadClasses('renins', [
	\Renins\CompetitionBestName::class => 'lib/CompetitionBestName.php',
	\Renins\CompetitionProposal::class => 'lib/CompetitionProposal.php',
	\Renins\Employment::class => 'lib/Employment.php',
	\Renins\Feedback::class => 'lib/Feedback.php',
	\Renins\Focus::class => 'lib/Focus.php',
	\Renins\Helper::class => 'lib/Helper.php',
	\Renins\Project::class => 'lib/Project.php',
	\Renins\ProjectStatus::class => 'lib/ProjectStatus.php',
	\Renins\User::class => 'lib/User.php',
	\Renins\CRMAgents::class => 'lib/CRMAgents.php',
	\Renins\Vacancy::class => 'lib/Vacancy.php',
	\Renins\Agent\Boss::class => 'lib/Agent/Boss.php',
	\Renins\Agent\BossVacation::class => 'lib/Agent/BossVacation.php',
	\Renins\Agent\BossVacationHolidays::class => 'lib/Agent/BossVacationHolidays.php',
	\Renins\Agent\BossVacationLeaveDays::class => 'lib/Agent/BossVacationLeaveDays.php',
	\Renins\Agent\BossCities::class => 'lib/Agent/BossCities.php',
    \Renins\Agent\Functions::class => 'lib/Agent/Functions.php',
	\Renins\Agent\HealthAD::class => 'lib/Agent/HealthAD.php',
	\Renins\Agent\ClinicAD::class => 'lib/Agent/ClinicAD.php',
	\Renins\Agent\BuduAD::class => 'lib/Agent/BuduAD.php',
	\Renins\Agent\Bages::class => 'lib/Agent/Bages.php',
	\Renins\Agent\Jira::class => 'lib/Agent/Jira.php',
	\Renins\Agent\Ocenca360::class => 'lib/Agent/Ocenca360.php',
	\Renins\Agent\ADtoSocGroup::class => 'lib/Agent/ADtoSocGroup.php',
	\Renins\Agent\BossCostCenter::class => 'lib/Agent/BossCostCenter.php',
	\Renins\Agent\BossTimeSheet::class => 'lib/Agent/BossTimeSheet.php',
	\Renins\Agent\CostTopics::class => 'lib/Agent/CostTopics.php',
	\Renins\Ocenca360::class => 'lib/Ocenca360.php',
	\Renins\Stat\Stat::class => 'lib/Stat/Stat.php',
	\Renins\PersonalBlog\User::class => 'lib/PersonalBlog/User.php',
	\Renins\BP\TK::class => 'lib/BP/TK.php',
	\Renins\BP\Vacation::class => 'lib/BP/Vacation.php',
	\Renins\BP\IT::class => 'lib/BP/IT.php',
	\Renins\BP\Recruitment::class => 'lib/BP/Recruitment.php',
	\Renins\VacationYears::class => 'lib/VacationYears.php',
	\Renins\VacationHistory::class => 'lib/VacationHistory.php',
	\Renins\Toloka::class => 'lib/Toloka.php',
	\Renins\WorkPlaceBooking::class => 'lib/WorkPlaceBooking.php',
	\Renins\IB::class => 'lib/IB.php',
	\Renins\HL::class => 'lib/HL.php',
	\Renins\Tasks::class => 'lib/Tasks.php',
	\Renins\Privileges\Base::class => 'lib/Privileges/Base.php',
	\Renins\Privileges\Dms::class => 'lib/Privileges/Dms.php',
	\Renins\Privileges\Ns::class => 'lib/Privileges/Ns.php',
	\Renins\Agent\Duplicates::class => 'lib/Agent/Duplicates.php',
	\Renins\Agent\Maps::class => 'lib/Agent/Maps.php',
	\Renins\CheckUsers::class => 'lib/CheckUsers.php',
	\Renins\Component\BaseTemplateClass::class => 'lib/Component/BaseTemplateClass.php',
	\Renins\OnlineGid\OnlineGid::class => 'lib/OnlineGid/OnlineGid.php',
	\Renins\FormOne\Integration::class => 'lib/FormOne/Integration.php',
	\Renins\BP\Covid::class => 'lib/BP/Covid.php',
	\Renins\Orm\RequiterRequestHistoryTable::class => 'lib/orm/RequiterRequestHistory.php',
	\Renins\Orm\ProductionCalendarTable::class => 'lib/orm/ProductionCalendar.php',
	\Renins\Orm\BossCitiesTable::class => 'lib/orm/BossCities.php',
	\Renins\Orm\VacationsLogTable::class => 'lib/orm/VacationsLog.php',
	\Renins\Orm\VacationsCacheTable::class => 'lib/orm/VacationsCache.php',
	\Renins\Orm\UserToCityTable::class => 'lib/orm/UserToCity.php',
	\Renins\Orm\TaskToTaskTable::class => 'lib/orm/TaskToTask.php',
	\Renins\Orm\BossCostCenterTable::class => 'lib/orm/BossCostCenter.php',
	\Renins\Orm\OfficesTable::class => 'lib/orm/Offices.php',
	\Renins\Orm\BossAlternatesTable::class => 'lib/orm/BossAlternates.php',
	\Renins\Moex\Moex::class => 'lib/Moex/Moex.php',
	\Renins\Utils\PhpSpreadsheet::class => 'lib/utils/phpspreadsheet.php',
	\Renins\Landing::class => 'lib/Landing.php',
	\Renins\TimeSheet::class => 'lib/TimeSheet.php',
	\Renins\Paginator::class => 'lib/Paginator.php',
	\Renins\Integration\Boss::class => 'lib/integration/Boss.php',
	\Renins\Orm\TimeSheetTable::class => 'lib/orm/TimeSheet.php',
	\Renins\Orm\TimeSheetTypesTable::class => 'lib/orm/TimeSheetTypes.php',
	\Renins\Orm\TimeSheetCorrectionTable::class => 'lib/orm/TimeSheetCorrection.php',
	\Renins\Orm\TimeSheetOverworkTable::class => 'lib/orm/TimeSheetOverwork.php',
	\Renins\Orm\TimeSheetHistoryTable::class => 'lib/orm/TimeSheetHistory.php',
	\Renins\Orm\TimeSheetPeriodHistoryTable::class => 'lib/orm/TimeSheetPeriodHistory.php',
	\Renins\Orm\TimeSheetBlockEditTable::class => 'lib/orm/TimeSheetBlockEdit.php',
	\Renins\Orm\MyTeamsTable::class => 'lib/orm/MyTeams.php',
	\Renins\Orm\MyTeamsEntitiesTable::class => 'lib/orm/MyTeamsEntities.php',
	\Renins\Agent\MyTeams::class => 'lib/Agent/MyTeams.php',
	\Renins\TimeSheetOverwork::class => 'lib/TimeSheetOverwork.php',
	\Renins\Orm\TimeSheetOverworkLogTable::class => 'lib/orm/TimeSheetOverworkLog.php',
]);