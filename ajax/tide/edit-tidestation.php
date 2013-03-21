<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/service/TideStationService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';

$id = $_POST['stationid'];
$name = $_POST['stationname'];

TideStationService::editStation($id, $name);
header("Location:".Path::toTideStations());
?>