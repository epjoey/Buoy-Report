<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';



$user = UserService::getUser();

if (isset($_GET['reporter']) && $_GET['reporter']) {
	$reporterId = $_GET['reporter'];			
}

$isReporterLocations = false;
if (isset($reporterId)) {
	$isReporterLocations = TRUE;
}	

$isCurrentUserLocations = false;
if ($user->isLoggedIn && $reporterId == $user->id) {
	$isCurrentUserLocations = TRUE;
}

if ($isCurrentUserLocations) {
	$locations = $user->locations;

} elseif ($isReporterLocations) {

	$reporter = ReporterService::getReporter($reporterId);
	if (!$reporter) {
		header("HTTP/1.0 404 Not Found");
		include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
		exit();
	}			
	$locations = LocationService::getReporterLocations($reporterId);

} else {
	$locations = LocationService::getAllLocations();

}

if (isset($_GET['post']) && $_GET['post'] == 'true') {
	$isToPost = TRUE;
}




$page = new LocationPage();
$page->renderPage(array(
	'pageTitle' => 'Locations',
	'user' => $user,
	'locations' => $locations,
	'isToPost' => $isToPost,
	'isCurrentUserLocations' => $isCurrentUserLocations,
	'isReporterLocations' => $isReporterLocations,
	'reporterId' => $reporterId,
	'device' => $device,
	'reporter' => $reporter
));
?>