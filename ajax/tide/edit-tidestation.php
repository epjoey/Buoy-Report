<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$id = $_POST['stationid'];
$name = $_POST['stationname'];

TideStationService::editStation($id, $name);
header("Location:".Path::toTideStations());
?>