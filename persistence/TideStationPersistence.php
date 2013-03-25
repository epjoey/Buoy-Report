<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideStationPersistence {
	static function getTideStations($ids) {
		
		$ids = Utils::compact($ids);
		if (count($ids) <= 0) {
			return array();
		}

		$ids = array_map('Persistence::escape', $ids);
		
		$tideStations = ModelCache::get('TideStation', $ids);
		$uncachedIds = array_diff($ids, array_keys($tideStations));
		
		if (!$uncachedIds) {
			return $tideStations;
		}
		$idStr = implode(',', $uncachedIds);
		$where = " WHERE stationid in ($idStr)";
		$sql = "SELECT * FROM tidestation $where";
		$result = Persistence::run($sql);
		while ($row = mysqli_fetch_object($result)) {	
			$tideStations[] = new TideStation($row);

			$tideStation = new TideStation($row);
			$tideStations[$tideStation->stationid] = $tideStation;
			error_log("TideStation " . $tideStation->stationid . " used db");
			ModelCache::set('TideStation', $tideStation->stationid, $tideStation);			
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