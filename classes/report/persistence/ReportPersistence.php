<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/model/Report.php';

class ReportPersistence {
	
	static function updateReport($report) {
		$link = Persistence::dbConnect();
		$reportId = intval($report->id);
		
		//update quality field
		$fields = " quality = '" . intval($report->quality) . "'";
		
		//update text field
		$fields .= ", text = '" . mysqli_real_escape_string($link, $report->text) . "'";			

		//update imagepath field
		$imagepath = $report->imagepath ? mysqli_real_escape_string($link, $report->imagepath) : "NULL";
		$fields .= ", imagepath = '" . $imagepath . "'";					
		
		//update waveheight field
		$fields .= ", waveheight = '" . floatval($report->waveheight) . "'";			

		//update sublocation field
		$fields .= ", sublocationid = '" . intval($report->sublocationid) . "'";			

		$sql = "UPDATE report SET $fields WHERE id = '$reportId'";	
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error updating report" . mysqli_error($link));
		}
	}

	static function getReport($id) {
		//get data
		//return new Report($data);
	}

	static function getReports($ids, $options = array()) {
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'obsdate DESC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		if ($ids) {
			$where = " WHERE id in ($ids)";
		}
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM report $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$reports = array();
		while ($row = mysqli_fetch_object($result)) {	
			$reports[] = new Report($row);
		}
		return $reports;	
	}

	static function insertReport($report) {
		$link = Persistence::dbConnect();

		$public = intval($report->public);
		$locationid = intval($report->locationid);
		$reporterid = intval($report->reporterid);
		$obsdate = intval($report->obsdate);
		$reportdate = intval($report->reportdate);
		$fields = "locationid = '$locationid', reporterid = '$reporterid', public = '$public', obsdate = '$obsdate', reportdate = '$reportdate'";
		
		if ($report->quality) {
			$quality = intval($report->quality);
			$fields .= ", quality = '" . $quality . "'";
		}
		if ($report->text) {
			$text = mysqli_real_escape_string($link, $report->text);
			$fields .= ", text = '" . $text . "'";			
		}
		if ($report->imagepath) {
			$imagepath = mysqli_real_escape_string($link, $report->imagepath);
			$fields .= ", imagepath = '" . $imagepath . "'";			
		}
		if ($report->waveheight) {
			$waveheight = floatval($report->waveheight);
			$fields .= ", waveheight = '" . $waveheight . "'";			
		}	
		if ($report->sublocationid) {
			$sublocationId = intval($report->sublocationid);
			$fields .= ", sublocationid = '" . $sublocationId . "'";			
		}				
		$sql = "INSERT INTO report SET $fields";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting report into DB" . mysqli_error($link));
		}
		return mysqli_insert_id($link);
	}

	//todo: delete buoy_report/tide_report matrix table data (before 1/17/13, this was deleting actual buoy/tide data...not anymore)
	static function deleteReport($id) {
		$link = Persistence::dbConnect();
		$reportId = intval($id);
		$sql = "DELETE FROM report WHERE id = '$reportId'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error deleting report" . mysqli_error($link));
		}
	}

}
?>