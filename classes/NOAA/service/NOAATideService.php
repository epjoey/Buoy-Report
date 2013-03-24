<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/NOAA/persistence/NOAATidePersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/exceptions/InternalException.php';

class NOAATideService {
	static function getTideDataFromStationAtTime($stationId, $time) {
		if (!$stationId || !$time) {
			throw new InternalException();
		}
		return NOAATidePersistence::getTideDataFromStationAtTime($stationId, $time);
	}	
}
?>