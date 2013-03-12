<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';

$reportFilters = FilterService::getReportFilterRequests();
$reports = Persistence::getReports($reportFilters, 6, $_REQUEST['offset']);
ReportFeed::renderReports($reports);
?>
