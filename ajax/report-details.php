<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';

$id = $_REQUEST['id'];

$report = new SingleReport; //don't need to loadData

if ($_POST['imagePath'] != '') {
	$report->renderImage($_POST['imagePath']);	
} 
if ($_POST['buoys'] != 'FALSE') {
	$report->renderBuoyDetails($id, $_POST['timezone']);	
} 
if ($_POST['tideStation'] != 'FALSE') {
	$report->renderTideDetails($id, $_POST['tideStation'], $_POST['timezone']);	
}
$report->renderReporterDetails($id, $_POST['reporterId'], $_POST['reportTime'], $_POST['timezone']);

?>
