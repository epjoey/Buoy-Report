<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$locationId = $_POST['locationId'];
$location = LocationService::getLocation($locationId);

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

?>