<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

header('Content-Type: application/json');
//todo: check if buoy is legit, check max buoys per location
$return = array();
$return['success'] = false;
$return['status'] = '';

$locationId = $_POST['locationid'];
$buoyId = $_POST['buoyid'];
$buoyName = $_POST['buoyname'];
if (!$locationId) {		
	$return['status'] = 'no location';
	//print json_encode($return);
	header("Location:".Path::toLocation($locationId));
	exit;
}
if (!$buoyId) {		
	$return['status'] = 'no buoy';
	//print json_encode($return);
	header("Location:".Path::toLocation($locationId));
	exit;
}


if (!BuoyService::buoyExists($buoyId)) {
	try {
		BuoyService::addBuoy($buoyId, $buoyName);
	} catch (AddStationException $e) {
		$return['status'] = $e->getMessage();
		header("Location:".Path::toLocation($locationId));
	}
}

try {

	BuoyService::addBuoyToLocation($buoyId, $locationId);
	$return['status'] = $buoyId . ' added to ' . $locationId;
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