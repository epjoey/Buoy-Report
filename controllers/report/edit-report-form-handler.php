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
		$uploadStatus = handleFileUpload($_FILES['upload'], $report->reporterid);

		/* redirect back to form if handleFileUpload returns error */
		if (isset($uploadStatus['error'])) {
			throw new Exception($uploadStatus['error']);	
		}

		/* store image path in post if saved succesfully */
		$report->imagepath = $uploadStatus['imagepath'];
	}
	/* in case they used picup, its a remote url */	
	else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
		$report->imagepath = rawurldecode($_POST['remoteImageURL']);
	}

} catch(Exception $e) {
	header('Location:'.Path::toEditReport($report->id, $e->getMessage()));
	exit;

}

ReportService::updateReport($report);
header('Location:'.Path::toSingleReport($report->id));
?>