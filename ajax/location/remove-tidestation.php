<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/service/TideStationService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

header('Content-Type: application/json');
//todo: check if buoy is legit, check max buoys per location
$return = array();
$return['success'] = false;
$return['status'] = '';

$locationId = $_POST['locationid'];
$stationId = $_POST['stationid'];
if (!$locationId) {		
	$return['status'] = 'need location';
	print json_encode($return);
	exit;
}
if (!$stationId) {		
	$return['status'] = 'need station';
	print json_encode($return);
	exit;
}

TideStationService::removeStationFromLocation($stationId, $locationId);

$return['success'] = true;
//print json_encode($return);

header("Location:".Path::toLocation($locationId));
?>