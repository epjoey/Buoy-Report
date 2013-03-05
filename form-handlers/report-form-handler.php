<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/service/ReportService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidedata/service/TideDataService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/service/BuoyDataService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/model/Report.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';

/* --------------- HANDLE REPORT FORM SUBMISSION --------------- */

$report = new Report($_POST);
try {
	

	//feature not yet live
	if (isset($_POST['arbitrary_date'])) {
		$date = $_POST['arbitrary_date'];
		//if ($date not in format) { throw new ... }
		$report->obsdate = intval(gmdate("U", time(strtotime($date))));
	} else {
		/* calculates date of observation if in the past */	
		$offset = abs(intval($_POST['time_offset'])) * 60 * 60;
		$report->obsdate = intval(gmdate("U", time()-$offset));			
	}			
			
	if (isset($_FILES['upload']['tmp_name']) && $_FILES['upload']['tmp_name'] !='') {
		
		$uploadStatus = handleFileUpload($_FILES['upload'], $report->reporterid);

		if (isset($uploadStatus['error'])) {
			throw new Exception($uploadStatus['error']);	
		}
		$report->imagepath = $uploadStatus['imagepath'];
	}

	/* in case they used picup, its a remote url */	
	else if (isset($_POST['remoteImageURL']) && $_POST['remoteImageURL'] !='') {
		$report->imagepath = rawurldecode($_POST['remoteImageURL']);
	}	

	$report->id = ReportService::saveReport($report);

	$tideStations = $_POST['tidestations'];
	if ($tideStations) {
		TideDataService::getAndSaveTideDataForReport($report, $tideStations);
	}

	$buoys = $_POST['buoys'];
	if ($buoys) {
		BuoyDataService::getAndSaveBuoyDataForReport($report, $buoys);
	}	

	//add location to user's locations	
	if (!$_POST['reporterHasLocation']) {
		Persistence::insertUserLocation($report->reporterid, $report->locationid);		
	}

} catch (Exception $e) {
	header('Location:'.Path::toPostReport($report->locationid, $e->getMessage()));
	exit;

}

/* redirect to user home page where page will look for session[new-report] and load via ajax. */
header('Location:'.Path::toUserHome());
?>