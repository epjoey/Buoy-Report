<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyReportPersistence {

	static function getSavedBuoyReportsForReport($report, $options = array()) {
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
		$buoyReports = array();
		while ($row = mysqli_fetch_object($result)) {	
			$buoyReports[] = new BuoyReport($row);
		}
		return $buoyReports;		
	}

	/* properties could be "MM" so need to escape and not use floatval or intval */
	static function insertBuoyReport($buoyReport) {
		$reportid = intval($buoyReport->reportid);
		$set = "reportid = '$reportid'";
		$buoyReportArr = get_object_vars($buoyReport);
		foreach($buoyReportArr as $key=>$val) {
			if (in_array($key, array('buoy','winddir','windspeed','swellheight','swelldir','swellperiod','tide','watertemp','gmttime'))) {
				$val = Persistence::escape($val);
				$set .= ", $key = '$val'";
			}
		}
		Persistence::run("INSERT INTO buoydata SET $set");
	}

}



?>