<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

header('Content-Type: application/json');

$return = array();
$return['success'] = false;
$return['status'] = '';

$locationId = $_POST['locationid'];
$buoyId = $_POST['buoyid'];
$buoyName = $_POST['buoyname'];

if (!$buoyId) {		
	$return['status'] = 'no buoy';
	print json_encode($return);
	exit;
}


//do this even if no locationid, so that we can use this endpoint for simply adding buoys to db
if (!BuoyService::buoyExists($buoyId)) {
	try {
		BuoyService::addBuoy($buoyId, $buoyName);
	} catch (AddStationException $e) {
		$return['status'] = $e->getMessage();
		print json_encode($return);
		exit;
	}
}

if (!$locationId) {		
	header("Location:".Path::toBuoys());
	exit;
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