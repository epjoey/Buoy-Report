<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

$user = UserService::getUser();
$report = ReportService::getReport($id);
$report->location = LocationService::getLocation($report->locationid, array('includeSublocations'=>TRUE));

if(($report->reporterid && $report->reporterid != $user->id) || !$report) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}	

$reportFormStatus = StatusMessageService::getStatusMsgForAction('edit-report-form');
StatusMessageService::clearStatusForAction('edit-report-form');


$page = new EditReportPage();
$page->renderPage(array(
	'pageTitle' => 'Edit Report',
	'user' => $user,
	'report' => $report,
	'device' => $device,
	'reportFormStatus' => $reportFormStatus
));
?>