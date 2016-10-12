<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  

$buoyId = $_REQUEST['buoyid'];
$buoyReports = BuoyReportService::getBuoyReports($buoyId, array(
  'limit' => 24,
  'offset' => new DateTime(),
  'preCheckOnline' => false
));
header('Content-type: application/json');
print json_encode($buoyReports);
?>