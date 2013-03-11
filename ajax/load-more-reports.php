<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';

$reportFilters = FilterService::getReportFilterRequests();

$offset = returnRequest('offset');

$reports = Persistence::getReports($reportFilters, 6, $offset);
ReportFeed::renderReports($reports);
?>
