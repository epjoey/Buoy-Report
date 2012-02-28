<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';


if (!isset($_SESSION)) session_start();

$report = $_SESSION['new-report'];
$report['id'] = Report::submitReport($report);

$loadDetails = TRUE; //preloads the details -- instead of through ajax
$reportLocation = Persistence::getLocationInfoById($report['locationid']); //doing it here instead of inside the class

$rep = new SingleReport;
$rep->loadData($report, $reportLocation, $loadDetails);
$rep->renderSingleReport();

unset($_SESSION['new-report']);
?>
