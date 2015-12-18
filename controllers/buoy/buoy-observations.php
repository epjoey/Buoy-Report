<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  

$stationId = $_REQUEST['stationid'];
$url = "http://www.ndbc.noaa.gov/data/realtime2/" . $stationId . ".spec";
$file = file($url);

$spaceRegx = "/[\s,]+/";

$keys = array_shift($file);
$keys = ltrim($keys, '#');
$keys = preg_split($spaceRegx, $keys);

$units = array_shift($file);
$units = ltrim($units, '#');
$units = preg_split($spaceRegx, $units);

// $data = array();
// foreach ($file as $row) {
//   $row = preg_split($spaceRegx, $row);
//   $newRow = array();
//   for ($i=0; $i < count($keys); $i++) {
//     $newRow[$keys[$i]] = $row[$i];
//   }
//   $data[] = $newRow;
// }

print json_encode($file);
?>