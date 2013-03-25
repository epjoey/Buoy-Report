<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
/*
 *	takes array of reporter ids and returns those reporters
 */
class ReporterPersistence {
	public static function getReporters($ids, $options = array()) {
		if (!$ids) {
			return array();
		}
		$defaultOptions = array(
			'start' => 0,
			'limit' => 150,
			'order' => 'id DESC'
		);
		$options = array_merge($defaultOptions, $options);
		$ids = array_map('intval', $ids);
		$ids = implode(',', $ids);
		$where = " WHERE id in ($ids)";
		$start = intval($options['start']);
		$limit = intval($options['limit']);
		$order = Persistence::escape($options['order']);
		$sql = "SELECT * FROM reporter $where ORDER BY $order LIMIT $start,$limit";
		$result = Persistence::run($sql);
		$reporters = array();
		while ($row = mysqli_fetch_object($result)) {	
			$reporters[] = new Reporter($row);
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