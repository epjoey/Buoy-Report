<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$locationId = $_GET['id'];
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

if ($location->parentLocationId) {
  $location->parentLocation = LocationService::getLocation($location->parentLocationId, array(
    'includeBuoys' => true,
    'includeTideStations' => true
  ));
  $location->buoys = array_merge($location->buoys, $location->parentLocation->buoys);
}

$user = UserService::getUser();
$page = new LocationSnapshotPage();
$page->renderPage(array(
	'pageTitle' => $location->locname,
	'user' => $user,
  'location' => $location,
));
?>