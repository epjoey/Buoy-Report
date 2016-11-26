<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/db_conf.php';

class PersistenceException extends Exception {}

class Persistence {
	static $dbLink = null;
	static $hashSalt = 'reportdb';
	static function dbConnect() {

		if (isset(self::$dbLink)) {
			return self::$dbLink;
		}

		$host = DB_HOST;
		$db = DB_NAME;
		$un = DB_USER;
		$pw = DB_PW;

		$link = mysqli_connect($host, $un, $pw);

		if (!$link) {
			die("Could not connect. " . mysqli_error());
		}

		if (!mysqli_set_charset($link, 'utf8'))	{
			die("Unable to set database connection encoding. " . mysqli_error());
		}

		if (!mysqli_select_db($link, $db)) {
			die("Unable to locate the reporter database" . mysqli_error());
		}
		self::$dbLink = $link;
		return $link;
	}
	static function sanitizeIds($ids) {
		$ids = Utils::compact($ids);
		$ids = array_map('addslashes', $ids);
		return $ids;
	}
	static function sanitizeId($id) {
		return addslashes($id);
	}
	static function escape($str) {
		return addslashes($str);
	}
	static function run($sql, $options = array()) {
		$link = self::dbConnect();
		$result = mysqli_query($link, $sql);
		$errorMsg = isset($options['errorMsg']) ? $options['errorMsg'] : mysqli_error($link);
		if (!$result) {
			error_log($errorMsg . " sql:" . $sql);
			throw new PersistenceException($errorMsg);
		}
		return $result;
	}
	static function getModelsByProp($sql, $model, $prop, $options = array()) {
		$result = self::run($sql, $options);
		$objects = array();
		while ($object = mysqli_fetch_object($result)) {
			if (!isset($object->$prop)) {
				throw new PersistenceException("$prop is not a property of $model");
			}
			$objects[$object->$prop] = new $model($object);
		}
		return $objects;
	}
	static function getArray($sql, $options = array()) {
		$result = self::run($sql, $options);
		$arr = array();
		while ($row = mysqli_fetch_array($result)) {
			$arr[] = $row[0];
		}
		return $arr;
	}


/*==================================================== Locations ====================================================*/
/*===================================================================================================================*/

	public static function insertLocation($locname, $timezone=NULL, $reporterid) {
		$link = Persistence::dbConnect();
		$locname = mysqli_real_escape_string($link, $locname);
		$reporterid = intval($reporterid);
		if (isset($timezone)) {
			$tz = ", timezone = '$timezone'";
		} else $tz = "";
		$sql = "INSERT INTO location SET locname = '$locname'" . $tz . ", creator = '$reporterid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting location into DB" . mysqli_error($link));
		}
		$newLocation = mysqli_insert_id($link);
		$sql = "INSERT INTO reporterlocation SET reporterid = '$reporterid', locationid = '$newLocation'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting location into DB" . mysqli_error($link));
		}
		return $newLocation;
	}

	public static function getLocations() {
		$link = Persistence::dbConnect();
		$sql = "SELECT * FROM location ORDER BY locname ASC";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching locations" . mysqli_error($link));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$locations[] = $row;
		}
		if (!empty($locations)) {
			return $locations;
		} else return NULL;
	}

	public static function getLocationInfoById($locationId) {
		$link = Persistence::dbConnect();
		$locationId = intval($locationId);
		$sql = "SELECT * FROM location WHERE id = '$locationId'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching buoys by location" . mysqli_error($link));
		}
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		if (!empty($row)) {
			return $row;
		} else return NULL;
	}

	//otimize this call. use nested select
	public static function getUserLocations($reporterid) {
		$link = Persistence::dbConnect();
		$reporterid = intval($reporterid);
		$sql = "SELECT locationid FROM reporterlocation WHERE reporterid = '$reporterid'";
		$result = mysqli_query($link, $sql) or die('error fetching locations of user');
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$locations[] = $row;
		}
		if (empty($locations)) {
			return array();
		}

		//construct where clause based on location id's

		$where = ' WHERE';
		$numLocations = count($locations);
		for($i = 0; $i < $numLocations; $i++) {
			$id = $locations[$i]['locationid'];
			$where .= " id = '$id'";
			if (isset($locations[$i+1])) {
				$where .= ' OR';
			}

		}

		$sql = "SELECT * FROM location" . $where;
		//var_dump($sql);
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error getting user locations" . mysqli_error($link));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$locationInfo[] = $row;
		}
		return $locationInfo;
	}

	public static function removeLocationFromUser($locationid, $reporterid) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$reporterid = intval($reporterid);
		$sql = "DELETE FROM reporterlocation WHERE reporterid = '$reporterid' AND locationid = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error deleting bookmark" . mysqli_error($link));
		}
	}


	public static function dbContainsLocation($locname) {
		$link = Persistence::dbConnect();
		$locname = mysqli_real_escape_string($link, $locname);
		$sql = "SELECT COUNT(*) FROM location WHERE locname = '$locname'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error searching for location in db");
		}
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function insertLocationForecastUrl($locationid, $forecastUrl) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$forecastUrl = mysqli_real_escape_string($link, $forecastUrl);
		$sql = "INSERT INTO locationforecast SET locationid = '$locationid', forecasturl = '$forecastUrl'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting location forecast link into DB" . mysqli_error($link));
		}
	}

	public static function deleteLocationForecastUrl($locationid, $urls) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$string = '';
		foreach($urls as $key=>$url) {
			$url = mysqli_real_escape_string($link, $url);
			$string .= "forecasturl = '" . $url . "'";
			if (isset($urls[$key+1])) {
				$string .= " OR ";
			}
		}
		$sql = "DELETE FROM locationforecast WHERE locationid = '$locationid' AND ($string)";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error deleting location forecast link from DB" . mysqli_error($link));
		}
	}


	public static function getForecastUrlsByLocationId($locationid) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$sql = "SELECT forecasturl FROM locationforecast WHERE locationid = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error retrieving forecast links from DB" . mysqli_error($link));
		}
		$urls = array();
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$urls[] = $row['forecasturl'];
		}
		return $urls;
	}

	public static function dbContainsLocationForecast($locationid, $forecastUrl) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$sql = "SELECT COUNT(*) FROM locationforecast WHERE locationid = '$locationid' AND forecasturl = '$forecastUrl'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error searching db for forecast links" . mysqli_error($link));
		}
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function deleteLocation($id) {
		$link = Persistence::dbConnect();
		$id = intval($id);
		$sql = "DELETE FROM location WHERE id = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting location");
		$sql = "DELETE FROM reporterlocation WHERE locationid = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting location associations");
		$sql = "DELETE FROM report WHERE locationid = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting location report associations");
	}



/*================================================== Sub Locations ==================================================*/
/*===================================================================================================================*/

	public static function getSubLocationsByLocation($id) {
		$link = Persistence::dbConnect();
		$id = intval($id);
		$sql = "SELECT a.*
				FROM sublocation a
				INNER JOIN locationsublocation b ON a.sl_id = b.sublocationid
				WHERE b.locationid = '$id'";
		$result = mysqli_query($link, $sql);
		$rows = array();
		while ($row = mysqli_fetch_object($result)) {
			$rows[] = $row;
		}
		return $rows;
	}


/*==================================================== Users ====================================================*/
/*===================================================================================================================*/




	public static function makeAllUserReportsPublic($userId){
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "UPDATE report SET public = '1' WHERE reporterid = '$userId'";
		$result = mysqli_query($link, $sql) or die("Error updating user account" . mysqli_error($link));
	}

	public static function makeAllUserReportsPrivate($userId){
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "UPDATE report SET public = '0' WHERE reporterid = '$userId'";
		$result = mysqli_query($link, $sql) or die("Error updating user account" . mysqli_error($link));
	}

	public static function databaseContainsEmail($email) {
		$link = Persistence::dbConnect();
		$email = mysqli_real_escape_string($link, $email);
		$result = mysqli_query($link, "SELECT COUNT(*) FROM reporter WHERE email='$email'");
		if (!$result) die("Error searching for email" . mysqli_error($link));
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) return TRUE;
		else return FALSE;
	}

	public static function databaseContainsName($name) {
		$link = Persistence::dbConnect();
		$name = mysqli_real_escape_string($link, $name);
		$result = mysqli_query($link, "SELECT COUNT(*) FROM reporter WHERE name='$name'");
		if (!$result) die("Error searching for email" . mysqli_error($link));
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) return TRUE;
		else return FALSE;
	}

	public static function returnUserId($username, $password) {
		$link = Persistence::dbConnect();
		$username = mysqli_real_escape_string($link, $username);
		$sql = "SELECT id FROM reporter WHERE name='$username'";
		$password = md5($password . Persistence::$hashSalt);
		$password = mysqli_real_escape_string($link, $password);
		$sql .= " AND password='$password'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching reporter id");
		}
		$row = mysqli_fetch_array($result);
		if (isset($row['id'])) {
			return $row['id'];
		} else {
			return NULL;
		}
	}


	public static function insertUserCookie($userId, $userKey) {
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "INSERT INTO usercookie SET userid = '$userId', userkey = '$userKey'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting user key");
		}
	}

	public static function userCookieExists($userId, $userKey) {
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "SELECT COUNT(*) FROM usercookie WHERE userid = '$userId' AND userkey = '$userKey'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error validating user cookie");
		}
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) return TRUE;
		else return FALSE;
	}

	public static function removeAllUserCookies($userId) {
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "DELETE FROM usercookie WHERE userid = '$userId'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error deleting user cookie");
		}
	}

	public static function replaceUserCookie($userId, $newKey, $curKey) {
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "UPDATE usercookie SET userkey = '$newKey', time = CURRENT_TIMESTAMP WHERE userid = '$userId' AND userkey = '$curKey'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error replacing user cookie");
		}


	}
}
?>