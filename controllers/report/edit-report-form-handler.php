<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

/* --------------- HANDLE EDIT REPORT FORM SUBMISSION --------------- */

$user = UserService::getUser();
if (!$user->isLoggedIn) {
	exit();
}

$report = ReportService::getReport($_POST['id']);
$report->waveheight = $_POST['waveheight'];
$report->quality = $_POST['quality'];
$report->text = $_POST['text'];
$report->sublocationid = $_POST['sublocationid'];

if ($_POST['submit'] == 'delete-report') {
	ReportService::deleteReport($report->id);
	header('Location:'.Path::toReports($user->id));
	exit();	
}

try {

	/* in case image was deleted on edit report form */
	if (isset($_POST['delete-image']) && $_POST['delete-image'] == 'true') {
		$report->imagepath = '';
	}
	
	if (isset($_POST['imageurl']) && $_POST['imageurl'] !='') {
		$report->imagepath = rawurldecode($_POST['imageurl']);
	}	

} catch(InvalidSubmissionException $e) {
	StatusMessageService::setStatusMsgForAction($e->getMessage(), 'edit-report-form');
	header('Location:'.Path::toEditReport($report->id));
	exit;

}

ReportService::updateReport($report);
header('Location:'.Path::toSingleReport($report->id));
?>