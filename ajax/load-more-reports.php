<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/ReportFeed.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/service/FilterService.php';

$reportFilters = FilterService::getReportFilterRequests();

$offset = returnRequest('offset');

//var_dump($filters);
$reports = Persistence::getReports($reportFilters, 6, $offset);
ReportFeed::renderReports($reports);
?>
