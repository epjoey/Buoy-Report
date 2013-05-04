<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

header('Content-Type: application/json');
//todo: check if buoy is legit, check max buoys per location
$return = array();
$return['success'] = false;
$return['status'] = '';

$locationId = $_POST['locationid'];
$stationId = $_POST['stationid'];
$stationName = $_POST['stationname'];

if (!$locationId) {		
	$return['status'] = 'no location';
	//print json_encode($return);
	header("Location:".Path::toLocation($locationId));
	exit;
}
if (!$stationId) {		
	$return['status'] = 'no buoy';
	//print json_encode($return);
	header("Location:".Path::toLocation($locationId));
	exit;
}


if (!TideStationService::stationExists($stationId)) {
	try {
		TideStationService::addStation($stationId, $stationName);
	} catch (AddTidestationExcetion $e) {
		$return['status'] = $e->getMessage();
		header("Location:".Path::toLocation($locationId));
	}
}

try {

	TideStationService::addStationToLocation($stationId, $locationId);
	$return['status'] = $stationId . ' added to ' . $locationId;
	$return['success'] = true;
	//print json_encode($return);

} catch (PersistenceException $e) {
	if (stristr($e->getMessage(), 'duplicate')) {
		$return['status'] = 'duplicate';
	}
	//print json_encode($return);
}	

header("Location:".Path::toLocation($locationId));
?>