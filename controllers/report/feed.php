<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);
$user = UserService::getUser();

$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
	'limit' => $_REQUEST['limit'],
	'start' => $_REQUEST['start']
));
ReportFeed::renderReports($reports);
?>