<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LocationPersistence {

	public static function getLocations($ids) {
		if (!$ids) {
			return array();
		}
		$ids = array_map('intval', $ids);
		$locations = ModelCache::get('Location', $ids);
		$uncachedIds = array_diff($ids, array_keys($locations));
		if (!$uncachedIds) {
			return $locations;
		}
		$idStr = implode(',', $uncachedIds);
		$sql = "SELECT a.*, b.locationid as parentLocationId
			FROM location a
			LEFT JOIN locationsublocation b ON b.sublocationid = a.id
			WHERE a.id in ($idStr)";
		$result = Persistence::run($sql);
		while ($row = mysqli_fetch_object($result)) {	
			$location = new Location($row);
			$locations[$location->id] = $location;
			//error_log("Location $location->id used db");
			ModelCache::set('Location', $location->id, $location);
		}
		return $locations;
	}

	public static function getSublocationIdsForLocation($locationId) {
		$id = intval($locationId);
		$sql = "SELECT sublocationid FROM locationsublocation WHERE locationid = '$id'";
		$result = Persistence::run($sql);
		$ids = array();
		while ($row = mysqli_fetch_array($result)) {
			$ids[] = $row[0];
		}
		return array_unique($ids);
	}

	public static function getSublocationsForLocation($location) {
		$ids = self::getSublocationIdsForLocation($location->id);
		return self::getLocations($ids);
	}

	static function updateLocation($location) {
		$id = intval($location->id);
		$tz = Persistence::escape($location->timezone);
		$locname = Persistence::escape($location->locname);
		$ip = Persistence::escape($location->coverImagePath);
		$sql = "UPDATE location SET 
				timezone = '$tz',
				locname = '$locname',
				coverImagePath = '$ip'
				WHERE id = '$id'";
		Persistence::run($sql);
		return true;
	}

	static function getAllLocationIds($options) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 1000,
			'order' => 'locname ASC'
		);
		$options = array_merge($defaultOptions, $options);		
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT id FROM location ORDER BY $order LIMIT $start,$limit";
		return Persistence::getArray($sql);		
	}
}
?>