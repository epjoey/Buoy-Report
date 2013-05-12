<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideReportPersistence {

	static function getTideReportsForReport($report, $options = array()) {
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
		$tideReports = array();
		while ($row = mysqli_fetch_object($result)) {	
			$tideReports[] = new TideReport($row);
		}
		return $tideReports;				
	}

	static function insertTideReport($tideReport) {
		$reportid = intval($tideReport->reportid);
		$date = intval($tideReport->tidedate);
		$tide = floatval($tideReport->tide);
		$rise = intval($tideReport->tideRise);
		$tidestation = Persistence::escape($tideReport->tidestation);
		$predictedTide = Persistence::escape($tideReport->predictedTide);
		$sql = "INSERT INTO tidedata SET reportid = '$reportid', tide = '$tide', predictedTide = '$predictedTide',  tideRise = '$rise', tidedate = '$date', tidestation = '$tidestation'";
		Persistence::run($sql);
	}

}



?>