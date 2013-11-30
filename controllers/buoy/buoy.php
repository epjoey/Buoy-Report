<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  

$buoyId = $_REQUEST['buoyid'];
$buoy = BuoyService::getBuoy($buoyId);
$buoy->buoyReports = BuoyReportService::getBuoyReports($buoy, array('limit'=> 20, 'time'=> new DateTime()));
header('Content-type: application/json');
print json_encode($buoy);
?>