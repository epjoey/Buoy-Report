<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/view/SingleReport.php';

$id = $_REQUEST['id'];

if ($_POST['imagePath'] != '') {
	SingleReport::renderImage($_POST['imagePath']);	
} 
if ($_POST['buoys'] != 'FALSE') {
	SingleReport::renderBuoyDetails($id, $_POST['timezone']);	
} 
if ($_POST['tideStation'] != 'FALSE') {
	SingleReport::renderTideDetails($id, $_POST['tideStation'], $_POST['timezone']);	
}
SingleReport::renderReporterDetails($id, $_POST['reporterId'], $_POST['reportTime'], $_POST['timezone']);

?>
