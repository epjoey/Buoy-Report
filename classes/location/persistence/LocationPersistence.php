<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location/model/Location.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location/model/Sublocation.php';

/*
 *	takes array of location ids and returns those locations
 */
class LocationPersistence {
	public static function getLocations($ids, $options = array()) {
		if (!$ids) {
			return array();
		}
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'locname ASC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$where = " WHERE id in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM location $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$locations = array();
		while ($row = mysqli_fetch_object($result)) {	
			$locations[] = new Location($row);
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
			'order' => 'id DESC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$where = " WHERE id in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM location $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$sublocations = array();
		while ($row = mysqli_fetch_object($result)) {	
			$sublocations[] = new Sublocation($row);
		}
		return $sublocations;		
	}
}
?>