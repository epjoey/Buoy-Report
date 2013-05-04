<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

header('Content-Type: application/json');
//todo: check if buoy is legit, check max buoys per location
$return = array();
$return['success'] = false;
$return['status'] = '';

$locationId = $_POST['locationid'];
$buoyId = $_POST['buoyid'];
if (!$locationId) {		
	$return['status'] = 'need location';
	print json_encode($return);
	exit;
}
if (!$buoyId) {		
	$return['status'] = 'need buoy';
	print json_encode($return);
	exit;
}

BuoyService::removeBuoyFromLocation($buoyId, $locationId);

$return['success'] = true;
//print json_encode($return);

header("Location:".Path::toLocation($locationId));
?>