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
$report = ReportService::getReport($id, array(
	'includeBuoyReports' => true,
	'includeTideReports' => true,
	'includeLocation' => true,
	'includeSublocation' => true,
	'includeBuoyModel' => true,
	'includeTideStationModel' => true,
	'includeReporter' => true
));
if(!$report) {
	header("HTTP/1.0 404 Not Found");			
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}




$page = new SingleReportPage();
$page->renderPage(array(
	'pageTitle' => $report->location->locname . ' report ' . $report->id,
	'report' => $report,
	'user' => $user
));
?>