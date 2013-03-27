<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$locationId = $_POST['locationId'];
$location = LocationService::getLocation($locationId);

if (empty($_POST['timezone']) || $_POST['timezone'] == $location->timezone) {
	$error = 3;
	header('Location:'.Path::toEditLocation($locationId, $error));
	exit();				
}			
Persistence::updateLocationTimezone($locationId, $_POST['timezone']);	
header('Location:'.Path::toEditLocation($locationId));

?>