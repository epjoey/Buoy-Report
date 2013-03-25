<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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

	static function addStation($id, $name) {
		if (!$id) {
			throw new AddStationException("New station must have id");
		}
		if (!$name) {
			throw new AddStationException("New station must have a name");
		}		
		if (!self::isValidStation($id)) {
			throw new AddStationException("$id is not a valid NOAA station");
		}

		TideStationPersistence::addStation($id, $name);
	}

	static function isValidStation($id) {
		return count(NOAATidePersistence::getLastTideReportFromStation($id) > 0);
	}	
}
?>