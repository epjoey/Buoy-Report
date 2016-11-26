<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  

const NUM_PER_PAGE = 24;
$buoyId = $_REQUEST['buoyid'];
$offset = get($_REQUEST, 'offset', 0);
$buoyReports = BuoyReportService::getBuoyReports($buoyId, array(
  'limit' => NUM_PER_PAGE,
  'offset' => intval($offset),
  'preCheckOnline' => false
));
header('Content-type: application/json');
print json_encode($buoyReports);
?>