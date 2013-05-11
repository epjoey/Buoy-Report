<?
header('Content-type: application/json');
    
$pageNum = $_REQUEST['page'] || 1;
$reporterId = $_REQUEST['reporter'];

$locations = LocationService::getAllLocations();

print json_encode($locations);
?>