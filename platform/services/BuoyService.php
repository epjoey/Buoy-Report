<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyService {

	static function getBuoysForLocation($location) {
		$ids = self::getBuoyIdsForLocation($location);
		$buoys = self::getBuoys($ids);
		return $buoys;
	}

	static function getBuoyIdsForLocation($location) {
		$id = intval($location->id);
		if (!$id) {
			return array();
		}
		$sql = "SELECT buoyid
				FROM buoy_location
				WHERE locationid = '$id'
				ORDER BY -sort_order desc";
		return Persistence::getArray($sql);
	}

	static function getBuoy($id) {
		$buoys = self::getBuoys(array($id));
		return reset($buoys);
	}

	static function getBuoys($buoyIds, $options = array()) {
		$ids = Persistence::sanitizeIds($buoyIds);
		if (!$ids) {
			return array();
		}
		$buoys = ModelCache::get('Buoy', $ids);
		$uncachedIds = array_diff($ids, array_keys($buoys));
		if (!$uncachedIds) {
			return sort_by_keys($buoys, $buoyIds);
		}
		//they could be alphanumerical
		$uncachedIds = array_map(function($id) {
			return "'" . $id . "'";
		}, $uncachedIds);
		$idStr = implode(",", $uncachedIds);
		$sql = "SELECT * FROM buoy WHERE buoyid in ($idStr)";
		$result = Persistence::run($sql);
		while ($row = mysqli_fetch_object($result)) {
			$buoy = new Buoy($row);
			$buoys[$buoy->buoyid] = $buoy;
			ModelCache::set('Buoy', $buoy->buoyid, $buoy);
		}
		return sort_by_keys($buoys, $buoyIds);
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
		if (!self::isValidBuoy($id)) {
			throw new AddStationException("$id is not a valid NOAA buoy");
		}
		BuoyPersistence::insertBuoy($buoy);
	}

	static function isValidBuoy($id) {
		return !!NOAABuoyReportPersistence::isBuoyOnline($id);
	}

	static function editBuoy($id, $name) {
		if (!$id || !$name) {
			throw new PersistenceException('edit needs $id, $name');
		}
		$id = Persistence::escape($id);
		$name = Persistence::escape($name);
		$sql = "UPDATE buoy SET name = '$name' WHERE buoyid = '$id'";
		Persistence::run($sql);
		return true;
	}

	static function deleteBuoy($id) {
		return BuoyPersistence::deleteBuoy($id);
	}

	static function sortLocationBuoys($locationId, $buoyIds) {
		$locationId = Persistence::sanitizeId($locationId);
		$ids = Persistence::sanitizeIds($buoyIds);
		foreach (array_values($buoyIds) as $i => $buoyId) {
			$sql = "UPDATE buoy_location SET sort_order = $i
				WHERE buoyid = '$buoyId'
				AND locationid = '$locationId'";
			Persistence::run($sql);
		}
		return true;
	}

}
?>