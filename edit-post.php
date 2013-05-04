<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/picup_functions.php';


if(isset($_REQUEST['id']) && $_REQUEST['id']) {
	$id = $_REQUEST['id'];
} else {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

$user = UserService::getUser();
$report = Persistence::getReportById($id);

if(($report['reporterid'] != $user->id) || !$report) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}		

$device = new Mobile_Detect();
if ($device->isAppleDevice()) {
	$needPicup = true;
}
//for picup callback. - mobile app redirection based on session var
if ($needPicup) {
	setPicupSessionId('edit-report-form', $id);
}

//old functions
$report['locationInfo'] = Persistence::getLocationInfoById($report['locationid']);
$report['locationInfo']['sublocations'] = Persistence::getSubLocationsByLocation($report['locationid']);


$reportFormStatus = StatusMessageService::getStatusMsgForAction('edit-report-form');
StatusMessageService::clearStatusForAction('edit-report-form');


$page = new EditPostPage();
$page->renderPage(array(
	'pageTitle' => 'Edit Report',
	'user' => $user,
	'report' => $report,
	'device' => $device,
	'needPicup' => $needPicup,
	'reportFormStatus' => $reportFormStatus
));
?>