<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/service/TideStationService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

$id = $_POST['stationid'];
$delete = $_POST['delete'];

if ($delete) {
	TideStationService::deleteStation($id);
}
header("Location:".Path::toTideStations());
?>