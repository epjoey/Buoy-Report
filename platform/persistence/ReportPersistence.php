<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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

	static function getReports($ids) {
		if (!$ids) {
			return array();
		}
		$ids = array_map('intval', $ids);
		$idStr = implode(',', $ids);
		$sql = "SELECT * FROM report WHERE id in ($idStr)";
		$reports = Persistence::getModelsByProp($sql, 'Report', 'id');
		$orderedReports = array();
		foreach($ids as $id) {
			$orderedReports[$id] = $reports[$id];
		}
		return $orderedReports;
	}

	static function insertReport($options) {
		$report = (object)$options;
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

	static function getReportIdsForUserWithFilters($user, $filters, $options = array()) {
		$defaultOptions = array(
			"start" => 0,
			"limit" => 6,
			"order" => "r.obsdate DESC"
		);
		$options = array_merge($defaultOptions, $options);
		$start = intval($options["start"]);
		$limit = intval($options["limit"]);
		$order = Persistence::escape($options["order"]);
		$where = array();
		$userId = intval($user->id);
		if ($userId) {
			$where[] = "(r.public = '1' OR r.reporterid = '$userId')";
		} else {
			$where[] = "r.public = '1'";
		}

		foreach($filters as $key => $val) {
			if (!$val) {
				continue;
			}
			switch ($key) {
				case 'quality':
					$where[] = "r.quality = " . intval($val);
					break;
				case 'image': 
					$where[] = $val == 1 ? "r.imagepath IS NOT NULL" : "r.imagepath IS NULL";
					break;
				case 'text':
					$where[] = "r.text LIKE '%" . Persistence::escape($val) . "%'";	
					break;
				case 'obsdate':
					$where[] = "r.obsdate <= " . strval(strtotime($val) + 59*60*24); //adding just under 24 hours to catch that day's reports
					break;
				case 'locationIds':
					$where[] = "r.locationid in (" . implode(',', array_map('intval',$val)) .")";
					break;	
				case 'reporterId':
					$where[] = "r.reporterid = " . intval($val);
					break;					
				case 'subLocationId':
					$where[] = "r.sublocationid = " . intval($val);
					break;
				case 'buoyIds':
					$where[] = "b.buoy in (" . implode(',', array_map('intval', array_map('intval',$val))) .")";
					break;
			}
		}
		$whereClause = implode(" AND ", $where);
		$sql = "SELECT r.id FROM report r ";
		$buoyIds = $filters['buoyIds'];
		if (isset($buoyIds) && $buoyIds) {
			$sql .= " INNER JOIN buoydata b ON r.id = b.reportid ";
		}
		$sql .= " WHERE $whereClause ORDER BY $order LIMIT $start,$limit"; 
		$ids = Persistence::getArray($sql);
		return $ids;
	}

}
?>