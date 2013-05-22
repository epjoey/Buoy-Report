<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyService {

	static function getBuoysForLocation($location) {
		$ids = BuoyPersistence::getBuoyIdsForLocation($location);
		return self::getBuoys($ids);
	}

	static function getBuoy($id) {
		return reset(self::getBuoys(array($id)));
	}

	static function getBuoys($buoyIds, $options = array()) {
		$defaultOptions = array(
			'numBuoyReportsToInclude' => 0
		);
		$options = array_merge($defaultOptions, $options);		
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

	static function addBuoy($id, $name) {
		if (!$id) {
			throw new AddStationException("New buoy must have id");
		}
		if (!$name) {
			throw new AddStationException("New buoy must have a name");
		}
		$buoy = new Buoy(array('buoyid'=>$id, 'name'=>$name));
		if (!self::isValidBuoy($buoy)) {
			throw new AddStationException("$id is not a valid NOAA buoy");
		}
		BuoyPersistence::addBuoy($buoy);
	}

	static function isValidBuoy($id) {
		return !!NOAABuoyReportPersistence::isBuoyOnline($id);
	}

	static function editBuoy($id, $name) {
		return BuoyPersistence::updateBuoy($id, $name);
	}

	static function deleteBuoy($id) {
		return BuoyPersistence::deleteBuoy($id);
	}
}
?>