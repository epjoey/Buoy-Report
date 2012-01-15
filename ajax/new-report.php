<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';


if (!isset($_SESSION)) session_start();

$newReport = Persistence::getReportById(Report::submitData($_SESSION['new-report']));
$newReportLocation = Persistence::getLocationInfoById($_SESSION['new-report']['locId']);
/* clearing session */
unset ($_SESSION['new-report']);


$showDetails = TRUE; //auto loads all ajax content
$report = new SingleReport;
$report->loadData($newReport, $newReportLocation, $showDetails);
$report->renderSingleReport();

?>
