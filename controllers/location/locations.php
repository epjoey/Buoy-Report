<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';  
header('Content-type: application/json');

$locations = LocationService::getAllLocations(array(
    // 'includeSublocations' => true,
    // 'includeBuoys' => true,
    // 'includeTideStations' => true
));
$locations = array_values($locations);
print json_encode($locations);
?>