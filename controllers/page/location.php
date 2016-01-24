<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$locationId = $_GET['location'];
if (!$locationId) {
	header('Location:'.Path::toLocations());
	exit();
}
$location = LocationService::getLocation($locationId, array(
	'includeSublocations' => true,
	'includeBuoys' => true,
	'includeTideStations' => true
));

if (!$location) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

$user = UserService::getUser();
$device = new Mobile_Detect();

/* load Report Filters */
$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);
$reportFilters['locationIds'] = array($location->id);

/* load Reports */
$numReportsPerPage = 6;
$reports = ReportService::getReportsForUserWithFilters($user, $reportFilters, array(
	'start' => 0,
	'limit' => $numReportsPerPage
));


$reportFormStatus = StatusMessageService::getStatusMsgForAction('submit-report-form');
StatusMessageService::clearStatusForAction('submit-report-form');

$page = new LocationDetailPage();
$page->renderPage(array(
	'pageTitle' => $location->locname,
	'user' => $user,
	'location' => $location,
	'creator' => ReporterService::getReporter($location->creator),
	'reportFilters' => $reportFilters,
	'numReportsPerPage' => $numReportsPerPage,
	'reports' => $reports,
	'device' => $device,
	'showReportForm' => isset($_REQUEST['report']) && $_REQUEST['report'],
	'reportFormStatus' => $reportFormStatus
));
?>