<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	exit_404_page();
}

$user = UserService::getUser();
$report = ReportService::getReport($id);
if(!$report) {
	exit_404_page();
}

$report->location = LocationService::getLocation($report->locationid, array('includeSublocations'=>TRUE));

if($report->reporterid && $report->reporterid != $user->id) {
	exit_404_page();
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