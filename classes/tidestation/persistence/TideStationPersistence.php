<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/model/TideStation.php';

class TideStationPersistence {
	static function getTideStations($ids, $options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('Persistence::escape', $ids);
		$ids = implode(',', $ids);
		if (!$ids) {
			return array();
		}		
		$where = " WHERE stationid in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$sql = "SELECT * FROM tidestation $where LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$tideStations = array();
		while ($row = mysqli_fetch_object($result)) {	
			$tideStations[] = new TideStation($row);
		}
		return $tideStations;	
	}	

	static function getAllTideStationIds($options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 1000
		);
		$options = array_merge($defaultOptions, $options);		
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$sql = "SELECT stationid FROM tidestation LIMIT $start,$limit";
		return Persistence::getArray($sql);
	}

	static function addStation($id, $name) {
		if (!$id || !$name) {
			throw new PersistenceException('addStation needs $id, $name');
		}
		$id = Persistence::escape($id);
		$name = Persistence::escape($name);
		$sql = "INSERT INTO tidestation SET stationid = '$id', stationname = '$name'";
		Persistence::run($sql);
		return true;		
	}
}
?>