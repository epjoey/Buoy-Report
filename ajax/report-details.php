<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/view/SingleReport.php';

$reportId = $_REQUEST['id'];

if ($_POST['imagePath'] != '') {
	SingleReport::renderImage($_POST['imagePath']);	
} 
if ($_POST['buoys'] != 'FALSE') {
	SingleReport::renderBuoyDetails($reportId, $_POST['timezone']);	
} 
if ($_POST['tideStation'] != 'FALSE') {
	SingleReport::renderTideDetails($reportId, $_POST['tideStation'], $_POST['timezone']);	
}
SingleReport::renderReporterDetails($reportId, $_POST['reporterId'], $_POST['reportTime'], $_POST['timezone']);


// $comments = ReportCommentPersistence::getCommentsForReport($reportId);

//SingleReport::renderComments($comments);


?>
