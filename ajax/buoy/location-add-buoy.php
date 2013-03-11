<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';

header('Content-Type: application/json');
//todo: check if buoy is legit, check max buoys per location
$return = array();
$return['success'] = false;
$return['status'] = '';

$locationId = $_POST['locationid'];
$buoyId = $_POST['buoyid'];
if (!$locationId) {		
	print json_encode($return);
	exit;
}
if (!$buoyId) {		
	print json_encode($return);
	exit;
}
try {

	BuoyService::addBuoyToLocation($buoyId, $locationId);
	$return['success'] = true;
	print json_encode($return);

} catch (PersistenceException $e) {
	if (stristr($e->getMessage(), 'duplicate')) {
		$return['status'] = 'duplicate';
	}
	print json_encode($return);
}	
?>