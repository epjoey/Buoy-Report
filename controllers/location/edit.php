<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
$locationId = $_POST['locationId'];
$location = LocationService::getLocation($locationId);

if ($_POST['submit'] == 'update-name') {
	if (empty($_POST['locname']) || $_POST['locname'] == $location->locname) {
		$error = 3;
		header('Location:'.Path::toEditLocation($locationId, $error));
		exit();				
	}
	if (Persistence::dbContainsLocation($_POST['locname'])) {
		$error = 4;
		header('Location:'.Path::toEditLocation($locationId, $error));
		exit();		
	}
	Persistence::updateLocationName($locationId, $_POST['locname']);			
	header('Location:'.Path::toEditLocation($locationId));
	exit();	
}

if ($_POST['submit'] == 'select-timezone') {
	if (empty($_POST['timezone']) || $_POST['timezone'] == $location->timezone) {
		$error = 3;
		header('Location:'.Path::toEditLocation($locationId, $error));
		exit();				
	}			
	Persistence::updateLocationTimezone($locationId, $_POST['timezone']);	
	header('Location:'.Path::toEditLocation($locationId));
	exit();	
}			

if ($_POST['submit'] == 'delete-location') {
	Persistence::deleteLocation($locationId);
	header('Location:'.Path::toUserHome());
	exit();	
}
?>