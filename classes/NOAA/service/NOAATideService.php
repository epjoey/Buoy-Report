<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/NOAA/persistence/NOAATidePersistence.php';
class NOAATideService {
	static function getTideDataFromStationAtTime($stationId, $time) {
		return NOAATidePersistence::getTideDataFromStationAtTime($stationId, $time);
	}	
}
?>