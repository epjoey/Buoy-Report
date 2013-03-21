<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/model/TideStation.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/persistence/TideStationPersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/exceptions/AddStationException.php';

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

	static function getAllTideStations() {
		$ids = TideStationPersistence::getAllTideStationIds();	
		return self::getTideStations($ids);
	}

	static function stationExists($stationId) {
		return count(self::getTideStations(array($stationId))) > 0;
	}

	static function addStationToLocation($stationId, $locationId) {
		$stationId = Persistence::escape($stationId);
		$locationId = intval($locationId);
		Persistence::run("INSERT INTO tidestation_location SET tidestationid='$stationId', locationid='$locationId'");

	}

	static function removeStationFromLocation($stationId, $locationId) {
		$stationId = Persistence::escape($stationId);
		$locationId = intval($locationId);
		Persistence::run("DELETE FROM tidestation_location WHERE tidestationid='$stationId' AND locationid='$locationId'");

	}	

	static function addTidestation($stationId, $stationName) {
		if (!$stationId) {
			throw new AddStationException("New station must have id");
		}
		if (!$stationName) {
			throw new AddStationException("New station must have a name");
		}		
		TideStationPersistence::addStation($buoyId, $buoyName);
	}
}
?>