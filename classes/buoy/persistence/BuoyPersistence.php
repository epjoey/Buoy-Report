<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/model/Buoy.php';

class BuoyPersistence {
	static function getBuoys($ids, $options = array()) {
		if (!$ids) {
			return array();
		}
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'buoyid ASC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$where = " WHERE buoyid in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM buoy $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$buoys = array();
		while ($row = mysqli_fetch_object($result)) {	
			$buoys[] = new Buoy($row);
		}
		return $buoys;	
	}

	static function getAllBuoys($options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 300,
			'order' => 'buoyid ASC'
		);
		$options = array_merge($defaultOptions, $options);		
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM buoy ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$buoys = array();
		while ($row = mysqli_fetch_object($result)) {
			$buoys[] = new Buoy($row);
		}
		return $buoys;	
	}	
}
?>