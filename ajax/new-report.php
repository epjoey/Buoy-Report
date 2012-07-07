<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';


if (!isset($_SESSION)) session_start();

$id = Report::submitReport($_SESSION['new-report']);

$report = Persistence::getReportbyId($id);

SingleReport::renderSingleReport($report, array('showDetails'=>true));

unset($_SESSION['new-report']);
?>
