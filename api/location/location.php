<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  
header('Content-type: application/json');
$locationId = $_REQUEST['location'];
if (!$locationId) {
  die("No Location ID");
}
$location = LocationService::getLocation($locationId);
print json_encode($location);
?>