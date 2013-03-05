<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/model/BuoyData.php';

class BuoyDataPersistence {

	static function getSavedBuoyDataForReport($report, $options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'id DESC'
		);
		$options = array_merge($defaultOptions, $options);
		$id = intval($report->id);
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM buoydata WHERE reportid = '$id' ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$buoyData = array();
		while ($row = mysqli_fetch_object($result)) {	
			$buoyData[] = new BuoyData($row);
		}
		return $buoyData;		
	}

	/* properties could be "MM" so need to escape and not use floatval or intval */
	static function insertBuoyData($buoyData) {
		$reportid = intval($buoyData->reportid);
		$set = "reportid = '$reportid'";
		$buoyDataArr = get_object_vars($buoyData);
		foreach($buoyDataArr as $key=>$val) {
			if (in_array($key, array('buoy','winddir','windspeed','swellheight','swelldir','swellperiod','tide','watertemp','gmttime'))) {
				$val = Persistence::escape($val);
				$set .= ", $key = '$val'";
			}
		}
		Persistence::run("INSERT INTO buoydata SET $set");
	}

}



?>