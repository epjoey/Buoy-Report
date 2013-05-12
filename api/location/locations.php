<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  
header('Content-type: application/json');

$pageNum = $_REQUEST['page'] || 1;
$reporterId = $_REQUEST['reporter'];
$locationId = $_REQUEST['location'];
if ($locationId) {
  $return = LocationService::getLocation($locationId);  
} else {
  $return = LocationService::getAllLocations();  
  $return = array_values($return);
}

print json_encode($return);
?>