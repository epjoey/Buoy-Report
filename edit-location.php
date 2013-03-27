<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$locationId = $_GET['location'];
if (!$locationId) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();
}

$user = UserService::getUser();

if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toEditLocation($locationId)));
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



if (isset($_GET['error']) && $_GET['error']) {
	switch($_GET['error']) {
		case 3: $editLocationError = "No Changes specified"; break;
		case 4: $editLocationError = "Location name specified already exists"; break;
	}
}

$page = new EditLocationPage();
$page->renderPage(array(
	'user' => $user,
	'pageTitle' => 'Edit: ' . $location->locname,
	'locationId' => $locationId,
	'location' => $location,
	'editLocationError' => $editLocationError
));


?>