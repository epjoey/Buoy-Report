<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

/* --------------- HANDLE REPORT FORM SUBMISSION --------------- */


/* populate report model object with post data.
 * $report will be entered into DB
 */

$user = UserService::getUser();
$report = new Report(array());
$report->reporterid = $user->id;
$report->public = $user->public;
$report->text = $_POST['text'];
$report->quality = $_POST['quality'];
$report->waveheight = $_POST['waveheight'];
$report->sublocationid = $_POST['sublocationid'];
$report->locationid = $_POST['locationid'];

try {
	
	/* either real date or date offset passed in from form */
	if (isset($_POST['time']) && $_POST['time']) {
		$reportDate = new DateTime($_POST['time']);
		if (!$reportDate) {
			throw new InvalidSubmissionException('date must be valid format');
		}
		if ($reportDate->getTimestamp() > time()) {
			throw new InvalidSubmissionException('date must be in the past');	
		}
		$report->obsdate = gmdate("U", $reportDate->getTimestamp());
	} else {
		$offset = abs(intval($_POST['time_offset'])) * 60 * 60; //offset is submitted in hours
		$report->obsdate = gmdate("U", time()-$offset);			

	}

	if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {
		$uploadStatus = handleFileUpload($_FILES['upload'], $user->id);
		if (isset($uploadStatus['error'])) {
			throw new InvalidSubmissionException($uploadStatus['error']);	
		}
		$report->imagepath = $uploadStatus['imagepath'];


	/* in case they used picup, its a remote url */	

	} else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
		$report->imagepath = rawurldecode($_POST['remoteImageURL']);
	}	

	$report = ReportService::saveReport($report, array(
		'buoyIds' => $_POST['buoys'],
		'tidestationIds' => $_POST['tidestations']
	));

} catch (InvalidSubmissionException $e) {
	header('Location:'.Path::toPostReport($report->locationid, $e->getMessage()));
	exit;

}

/* redirect to user home page where page will look for session[new-report] and load via ajax. */
header('Location:'.Path::toLocation($report->locationid));
?>