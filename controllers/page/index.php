<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';



$user = UserService::getUser();

if (!$user->isLoggedIn) {
	$page = new IntroPage();
	$page->renderPage(array(
		'pageTitle' => 'Welcome',
		'user' => $user,
		'detect' => new Mobile_Detect(),
	));
	exit();
}

$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);
$reportFilters['locationIds'] = Utils::pluck($user->locations, 'id');
$numReportsPerPage = 6;
$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
	'start' => 0,
	'limit' => $numReportsPerPage
));
$page = new HomePage();
$page->renderPage(array(
	'pageTitle' => 'Home',
	'user' => $user,
	'numReportsPerPage' => $numReportsPerPage,
	'reports' => $reports,
	'reportFilters' => $reportFilters
));
?>