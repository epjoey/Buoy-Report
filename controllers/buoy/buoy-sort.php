<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
$post = parse_post();
$locationId = get($post, 'locationId');
$buoyIds = get($post, 'buoyIds');

if(!$locationId || !$buoyIds || !is_array($buoyIds)){
  exit_405();
}

$location = LocationService::getLocation($locationId);
if(!$location){
  exit_404($request_body);
}
BuoyService::sortLocationBuoys($location->id, $buoyIds);

header('Content-type: application/json');
$result = array('success'=>TRUE);
print json_encode($result);
?>