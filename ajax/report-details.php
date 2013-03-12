<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/SingleReport.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/service/ReportService.php';

$reportId = $_REQUEST['id'];
$report = ReportService::getReport($reportId, array(
	'includeBuoyData' => true,
	'includeTideData' => true,
	'includeLocation' => true,
	'includeBuoyModel' => true,
	'includeTideStationModel' => true,
	'includeReporter' => true
));
SingleReport::renderImage($report);	
SingleReport::renderBuoyDetails($report);	
SingleReport::renderTideDetails($report);	
SingleReport::renderReporterDetails($report);


// $comments = ReportCommentPersistence::getCommentsForReport($reportId);

//SingleReport::renderComments($comments);


?>
