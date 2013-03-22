<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';


$user = new User;

$reportFilters = array(); 
$reportFilters['quality'] 	    = $_REQUEST['quality'];
$reportFilters['image']   	    = $_REQUEST['image'];
$reportFilters['text']    	    = $_REQUEST['text'];
$reportFilters['obsdate']    	= $_REQUEST['obsdate'];
$reportFilters['subLocationId'] = $_REQUEST['subLocationId'];
$reportFilters['locationId'] 	= $_REQUEST['locationId'];
$reportFilters['reporterId']	= $_REQUEST['reporterId'];
$reportFilters['locationIds']   = $_REQUEST['locationIds'];

/* load Reports */
$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters);
ReportFeed::renderReports($reports);

print 'broken';
//$reportFilters = FilterService::getReportFilterRequests();
//$reports = Persistence::getReports($reportFilters, 6, $_REQUEST['offset']);
//ReportFeed::renderReports($reports);
?>
