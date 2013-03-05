<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/model/Buoy.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/persistence/BuoyPersistence.php';

class BuoyService {

	static function getBuoysForLocation($location) {
		$id = intval($location->id);
		$sql = "SELECT a.* 
				FROM buoy a 
				INNER JOIN buoy_location b ON a.buoyid = b.buoyid 
				WHERE b.locationid = '$id'";
		$result = Persistence::run($sql);
		$buoys = array();
		while ($row = mysqli_fetch_object($result)) {
			$buoys[] = new Buoy($row);
		}
		return $buoys;
	}

	static function getBuoys($buoyIds) {
		return BuoyPersistence::getBuoys($buoyIds);
	}

	static function getAllBuoys($options = array()) {
		return BuoyPersistence::getAllBuoys($options);
	}

	static function addBuoyToLocation($buoyId, $locationId) {
		$buoyId = Persistence::escape($buoyId);
		$locationId = intval($locationId);
		Persistence::run("INSERT INTO buoy_location SET buoyid='$buoyId', locationid='$locationId'");

	}

	static function removeBuoyFromLocation($buoyId, $locationId) {
		$buoyId = Persistence::escape($buoyId);
		$locationId = intval($locationId);
		Persistence::run("DELETE FROM buoy_location WHERE buoyid='$buoyId' AND locationid='$locationId'");

	}	
}
?>