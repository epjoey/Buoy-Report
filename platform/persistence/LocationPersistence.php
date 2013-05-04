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
		$sql = "SELECT * FROM location WHERE id in ($idStr)";
		$result = Persistence::run($sql);
		while ($row = mysqli_fetch_object($result)) {	
			$location = new Location($row);
			$locations[$location->id] = $location;
			//error_log("Location $location->id used db");
			ModelCache::set('Location', $location->id, $location);

		}
		return $locations;
	}	

	public static function getSublocationsForLocation($location) {
		$id = intval($location->id);
		$sql = "SELECT a.* 
				FROM sublocation a 
				INNER JOIN locationsublocation b ON a.sl_id = b.sublocationid 
				WHERE b.locationid = '$id'";
		$result = Persistence::run($sql);
		$sublocations = array();
		while ($row = mysqli_fetch_object($result)) {
			$sublocations[] = new Sublocation($row);
		}
		return $sublocations;			
	}	

	public static function getSublocations($ids, $options = array()) {
		if (!$ids) {
			return array();
		}
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'sl_id DESC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$where = " WHERE sl_id in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM sublocation $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$sublocations = array();
		while ($row = mysqli_fetch_object($result)) {	
			$sublocations[] = new Sublocation($row);
		}
		return $sublocations;		
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