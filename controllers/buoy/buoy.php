<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  

$buoyId = $_REQUEST['buoyid'];
$buoyReports = BuoyReportService::getBuoyReports($buoyId, array(
  'limit' => 20,
  'time' => new DateTime(),
  'checkOnline' => false
));
header('Content-type: application/json');
print json_encode($buoyReports);
?>