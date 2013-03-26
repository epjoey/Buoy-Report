<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ReporterPersistence {
	public static function getReporters($ids) {
		if (!$ids) {
			return array();
		}
		$ids = array_map('intval', $ids);
		$reporters = ModelCache::get('Reporter', $ids);
		$uncachedIds = array_diff($ids, array_keys($reporters));
		if (!$uncachedIds) {
			return $reporters;
		}
		$idStr = implode(',', $uncachedIds);
		$where = " WHERE id in ($idStr)";
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM reporter $where";
		$result = Persistence::run($sql);
		while ($row = mysqli_fetch_object($result)) {	
			$reporter = new Reporter($row);
			$reporters[$reporter->id] = $reporter;
			//error_log("Reporter " . $reporter->id . " used db");
			ModelCache::set('Reporter', $reporter->id, $reporter);			
		}
		return $reporters;
	}

	static function reporterAddLocation($reporterid, $locationid) {
		if (!$reporterid || !$locationid) {
			throw new PersistenceException('missing args');
		}
		$link = Persistence::dbConnect();
		$reporterid = intval($reporterid);
		$locationid = intval($locationid);
		$sql = "INSERT INTO reporterlocation SET reporterid = '$reporterid', locationid = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting location bookmark into DB" . mysqli_error($link));
		}	
		return true;		
	}

	static function reporterHasLocation($rid, $lid) {
		if (!$rid || !$lid) {
			throw new PersistenceException('missing args');
		}
		$link = Persistence::dbConnect();
		$rid = intval($rid);
		$lid = intval($lid);
		$sql = "SELECT reporterid FROM reporterlocation WHERE reporterid = '$rid' AND locationid = '$lid' LIMIT 1";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error searching for user-location bookmark" . mysqli_error($link));
		}		
		$row = mysqli_fetch_array($result);	
		return $row[0] ? TRUE : FALSE;
	}

	public static function getReporterLocationIds($reporter) {
		if (!$reporter) {
			return array();
		}
		$rid = intval($reporter->id);
		$ids = Persistence::getArray("SELECT locationid FROM reporterlocation WHERE reporterid = $rid");
		return $ids;
	}	
}


?>