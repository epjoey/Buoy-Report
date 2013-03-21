<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/model/Buoy.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/persistence/BuoyPersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/exceptions/AddStationException.php';

class BuoyService {

	static function getBuoysForLocation($location) {
		$ids = BuoyPersistence::getBuoyIdsForLocation($location);
		return self::getBuoys($ids);
	}

	static function getBuoy($id) {
		return reset(self::getBuoys(array($id)));
	}

	static function getBuoys($buoyIds) {
		return BuoyPersistence::getBuoys($buoyIds);
	}

	static function getAllBuoys($options = array()) {
		$ids = self::getAllBuoyIds($options);
		return self::getBuoys($ids);
	}

	static function getAllBuoyIds($options = array()) {
		return BuoyPersistence::getAllBuoyIds($options);
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

	static function buoyExists($buoyId) {
		return count(self::getBuoys(array($buoyId))) > 0;
	}

	static function addBuoy($buoyId, $buoyName) {
		if (!$buoyId) {
			throw new AddStationException("New buoy must have id");
		}
		if (!$buoyName) {
			throw new AddStationException("New buoy must have a name");
		}		
		BuoyPersistence::addBuoy($buoyId, $buoyName);
	}
}
?>