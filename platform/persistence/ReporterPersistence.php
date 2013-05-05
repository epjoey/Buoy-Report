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
		$sql = "SELECT * FROM reporter WHERE id in ($idStr)";
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

	public static function getAllReporterIds() {
		$sql = "SELECT id FROM reporter";
		return Persistence::getArray($sql);
	}

	public static function getReporterIdsForLocation($location) {
		$lid = intval($location->id);
		if (!$lid) {
			throw new InvalidArgumentException();
		}
		$sql = "SELECT reporterid FROM reporterlocation WHERE locationid = $lid";
		return Persistence::getArray($sql);
	}

	public static function deleteReporter($reporter) {
		$id = intval($reporter->id);
		$sql = "DELETE FROM reporter WHERE id = '$id'";
		Persistence::run($sql, array('errorMsg' => "Error deleting reporter"));
		$sql = "DELETE FROM reporterlocation WHERE reporterid = '$id'";
		Persistence::run($sql, array('errorMsg' => "Error deleting reporter location associations"));
		$sql = "DELETE FROM report WHERE reporterid = '$id'";
		Persistence::run($sql, array('errorMsg' => "Error deleting reporter report associations"));
	}

	public static function updateReporter($reporter, $properties = array()) {
		$rid = $reporter->id;
		if (!$rid) {
			throw new InvalidArgumentException();
		}
		$set = array();
		if (isset($properties['name']) && $properties['name']) {
			$set[] = array('field' => 'name', 'value' => Persistence::escape($properties['name']));
		}
		if (isset($properties['email']) && $properties['email']) {
			$set[] = array('field' => 'email', 'value' => Persistence::escape($properties['email']));
		}	
		if (isset($properties['public']) && $properties['public']) {
			$set[] = array('field' => 'public', 'value' => intval($properties['public']));
		}			
		if (isset($properties['password']) && $properties['password']) {
			$set[] = array('field' => 'password', 'value' => md5($properties['password'] . Persistence::$hashSalt));
		}

		$str = "";
		for ($i = 0; $i < count($set); $i++) {
			$str .= $set[$i]['field'] . " = '" . $set[$i]['value'] . "'";
			$str .= isset($set[$i+1]) ? ", " : "";
		}
		//var_dump($set); exit;
		$sql = "UPDATE reporter SET " . $str . " WHERE id = '$rid'";		
		//var_dump($sql); exit;
		Persistence::run($sql);
	}


	public static function createReporter($name, $email, $password, $options = array()) {
		$defaultOptions = array(
			'reportPublicly' => true
		);
		$options = array_merge($defaultOptions, $options);

		$link = Persistence::dbConnect();
		$nameStr = "name = '" . mysqli_real_escape_string($link, $name) . "'";
		$emailStr = ", email = '" . mysqli_real_escape_string($link, $email) . "'";
		$passwordStr = ", password = '" . md5($password . Persistence::$hashSalt) . "'";
		$privacyStr = ", public = '" . intval($options['reportPublicly']) . "'";
		$sql = "INSERT INTO reporter SET " . $nameStr . $emailStr . $passwordStr . $privacyStr;
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting reporter into DB" . mysqli_error($link));
		}	
		return mysqli_insert_id($link);	
	}

}


?>