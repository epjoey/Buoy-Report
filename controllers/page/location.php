<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$locationId = $_GET['location'];
if (!$locationId) {
	header('Location:'.Path::toLocations());
	exit();
}
$location = LocationService::getLocation($locationId, array(
	'includeBuoys' => true,
	'includeTideStations' => true
));

if (!$location) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();	
}

if ($location->parentLocationId) {
	$location->parentLocation = LocationService::getLocation($location->parentLocationId, array(
		'includeBuoys' => true,
		'includeTideStations' => true
	));
}

$user = UserService::getUser();
$device = new Mobile_Detect();

$sublocationIds = LocationService::getSublocationIdsForLocation($location);
$location->sublocations = LocationService::getLocations($sublocationIds);

/* load Report Filters */
$reportFilters = ReportUtils::getFiltersFromRequest($_REQUEST);
$reportFilters['locationIds'] = array_merge(array($location->id), $sublocationIds);

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