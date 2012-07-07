<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';


//this end point is not currently being used (6.4.12)
$reportFilters = FilterService::getReportFilterRequests();

/* load Reports */
$reports = Persistence::getReports($reportFilters);

$filterResults = $reportFilters;
$filterResults['location'] = '';

ReportFeed::renderFeed($reports);
?>
