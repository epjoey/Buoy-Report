<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/model/TideStation.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/persistence/TideStationPersistence.php';

class TideStationService {

	static function getTideStationsForLocation($location) {
		$id = intval($location->id);
		$sql = "SELECT a.* 
				FROM tidestation a 
				INNER JOIN tidestation_location b ON a.stationid = b.tidestationid 
				WHERE b.locationid = '$id'";
		$result = Persistence::run($sql);
		$tideStations = array();
		while ($row = mysqli_fetch_object($result)) {
			$tideStations[] = new TideStation($row);
		}
		return $tideStations;
	}

	static function getTideStations($stationIds) {
		return TideStationPersistence::getTideStations($stationIds);
	}
}
?>