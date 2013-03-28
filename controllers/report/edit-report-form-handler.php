<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

/* --------------- HANDLE EDIT REPORT FORM SUBMISSION --------------- */

$report = ReportService::getReport($_POST['id']);
$report->waveheight = $_POST['waveheight'];
$report->quality = $_POST['quality'];
$report->text = $_POST['text'];

if ($_POST['submit'] == 'delete-report') {
	ReportService::deleteReport($report->id);
	header('Location:'.Path::toUserHome());
	exit();	
}

try {

	/* in case image was deleted on edit report form */
	if (isset($_POST['delete-image']) && $_POST['delete-image'] == 'true') {
		$report->imagepath = '';
	}
	
	/* handleFileUpload either saves photo and returns path, or returns an error */	
	if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {
		/* handleFileUpload either saves photo and returns path, or returns an error */	
		$report->imagepath = handleFileUpload($_FILES['upload'], $report->reporterid);
	}
	/* in case they used picup, its a remote url */	
	else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
		$report->imagepath = rawurldecode($_POST['remoteImageURL']);
	}

} catch(InvalidSubmissionException $e) {
	StatusMessageService::setStatusMsgForAction($e->getMessage(), 'edit-report-form');
	header('Location:'.Path::toEditReport($report->id));
	exit;

}

ReportService::updateReport($report);
header('Location:'.Path::toSingleReport($report->id));
?>