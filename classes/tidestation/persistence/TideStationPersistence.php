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
}
?>