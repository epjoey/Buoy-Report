<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();

$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);

$numReportsPerPage = 6;
$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
	'start' => 0,
	'limit' => $numReportsPerPage
));
$page = new ReportsPage();
$page->renderPage(array(
	'pageTitle' => 'Home',
	'user' => $user,
	'numReportsPerPage' => $numReportsPerPage,
	'reports' => $reports,
	'reportFilters' => $reportFilters
));
?>