<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/model/Buoy.php';

class BuoyPersistence {
	static function getBuoys($ids) {
		$ids = array_map('intval', $ids);
		if (!$ids) {
			return array();
		}
		$idStr = implode(',', $ids);
		$sql = "SELECT * FROM buoy WHERE buoyid in ($idStr)";
		$result = Persistence::run($sql);
		$buoys = array();
		while ($row = mysqli_fetch_object($result)) {	
			$buoys[] = new Buoy($row);
		}
		return $buoys;	
	}

	static function getAllBuoyIds($options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 1000,
			'order' => 'buoyid ASC'
		);
		$options = array_merge($defaultOptions, $options);		
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT buoyid FROM buoy ORDER BY $order LIMIT $start,$limit";
		return Persistence::getArray($sql);
	}

	static function getBuoyIdsForLocation($location) {
		$id = intval($location->id);
		if (!$id) {
			return array();
		}
		$sql = "SELECT buoyid 
				FROM buoy_location
				WHERE locationid = '$id'";
		return Persistence::getArray($sql);		
	}	

	static function addBuoy($buoyId, $buoyName) {
		$buoyId = Persistence::escape($buoyId);
		$buoyName = Persistence::escape($buoyName);
		$sql = "INSERT INTO buoy SET buoyid = '$buoyId', name = '$buoyName'";
		Persistence::run($sql);
		return true;
	}
}
?>