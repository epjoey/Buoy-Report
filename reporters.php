<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$location = null;
$user = UserService::getUser();

if (isset($_GET['location']) && $_GET['location']) {
	$locationId = $_GET['location'];	
	$location = LocationService::getLocation($locationId);
	if (!$location) {
		header("HTTP/1.0 404 Not Found");
		include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
		exit();
	} 
	$isLocationReporters = TRUE;
	$reporterIds = Persistence::getUsersByLocation($locationId);
	$reporters = array();
	foreach($reporterIds as $id) {
		$reporters[] = Persistence::getUserInfoById($id['reporterid']);
	} 
} else {
	$reporters = ReporterService::getAllReporters();
}
$page = new ReporterPage();
$page->renderPage(array(
	'pageTitle' => $location ? "$location->locname " : "" . 'Reporters',
	'user' => $user,
	'reporters' => $reporters,
	'location' => $location,
	'isLocationReporters' => $isLocationReporters,
	'device' => $device
));
?>