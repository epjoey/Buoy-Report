<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$id = $_POST['stationid'];
$delete = $_POST['delete'];

if ($delete) {
	TideStationService::deleteStation($id);
}
header("Location:".Path::toTideStations());
?>