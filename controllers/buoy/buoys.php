<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  

$locationId = $_REQUEST['location'];
$location = LocationService::getLocation($locationId);
$buoys = array_values(BuoyService::getBuoysForLocation($location));
foreach($buoys as $buoy) {
  $buoy->buoyReports = BuoyReportService::getBuoyReports($buoy, array('limit'=> 20, 'time'=> new DateTime()));
}
header('Content-type: application/json');
print json_encode($buoys);
?>