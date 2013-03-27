<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$reportFilters = array();
$reportFilters['quality']       = $_REQUEST['quality'];
$reportFilters['image']         = $_REQUEST['image'];
$reportFilters['text']          = $_REQUEST['text'];
$reportFilters['obsdate']       = $_REQUEST['obsdate'];
$reportFilters['locationIds']   = $_REQUEST['location'] ? array($_REQUEST['location']) : array();
$reportFilters['reporterId']    = $_REQUEST['reporterId'];
/* load Reports */

$user = UserService::getUser();

if ($_REQUEST['feed'] == 'homepage') {
	$reportFilters['locationIds'] = ReporterService::getReporterLocationIds($user);
}

$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
	'limit' => $_REQUEST['limit'],
	'start' => $_REQUEST['start'],
));
ReportFeed::renderReports($reports);

//$reportFilters = FilterService::getReportFilterRequests();
//$reports = Persistence::getReports($reportFilters, 6, $offset' =>);
//ReportFeed::renderReports($reports);
?>