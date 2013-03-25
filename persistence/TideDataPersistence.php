<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideDataPersistence {

	static function getSavedTideDataForReport($report, $options = array()) {
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
		$sql = "SELECT * FROM tidedata WHERE reportid = '$id' ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$tideData = array();
		while ($row = mysqli_fetch_object($result)) {	
			$tideData[] = new TideData($row);
		}
		return $tideData;				
	}

	static function insertTideData($tideData) {
		$reportid = intval($tideData->reportid);
		$date = intval($tideData->tidedate);
		$tide = floatval($tideData->tide);
		$rise = intval($tideData->tideRise);
		$tidestation = Persistence::escape($tideData->tidestation);
		$predictedTide = Persistence::escape($tideData->predictedTide);
		$sql = "INSERT INTO tidedata SET reportid = '$reportid', tide = '$tide', predictedTide = '$predictedTide',  tideRise = '$rise', tidedate = '$date', tidestation = '$tidestation'";
		Persistence::run($sql);
	}

}



?>