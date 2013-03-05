<?php

class PersistenceException extends Exception {}

class Persistence {
	static $dbLink = null;
	static function dbConnect() {

		if (isset(self::$dbLink)) {
			return self::$dbLink;
		}

		$host = 'localhost';
		$db = 'br';
		$un = 'root';
		$pw = 'ivytila';
					
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

/*==================================================== General Reports ====================================================*/
/*=========================================================================================================================*/
 
	public static function getReports($filters = array(), $limit = 6, $offset = 0) {
	
		//the basic SELECT statement
		$select = 'SELECT a.id as r_id, b.id as l_id, a.*, b.*, c.* ';
		$from = ' FROM report a 
					INNER JOIN location b ON a.locationid = b.id 
					LEFT JOIN sublocation c ON a.sublocationid = c.sl_id ';
		$where = " WHERE TRUE";
		$orderby = ' ORDER BY obsdate DESC';
		$limit = ' LIMIT ' . $offset . ',' . $limit;

		//use either specific id or list of ids for reporter clause
		if (isset($filters['reporterId'])) {
			$reporterId = intval($filters['reporterId']);
			$where .= " AND reporterid = '$reporterId' ";			
		}
		else if (!empty($filters['reporters'])) {
			$where .= " AND (";
			foreach ($filters['reporters'] as $key=>$reporter) {
				$where .= " reporterid = '$reporter'";
				if(isset($filters['reporters'][$key+1])) {
					$where .= " OR";
				}
			}
			$where .= ") "; 			
		}

		//use either specific id or list of ids for location clause
		if (isset($filters['locationId'])) {
			$locationId = intval($filters['locationId']);
			$where .= " AND locationid = '$locationId' ";			
		}
		else if (!empty($filters['locations'])) {
			$where .= " AND (";
			foreach ($filters['locations'] as $key=>$location) {
				$where .= " locationid = '$location'";
				if(isset($filters['locations'][$key+1])) {
					$where .= " OR";
				}
			}
			$where .= ") "; 			
		}			

		if (!empty($filters['sublocation'])) {
			$sublocation = intval($filters['sublocation']);
			$where .= " AND sublocationid = '$sublocation' ";			
		}

		if (isset($filters['quality'])) {
			$quality = intval($filters['quality']);
			$where .= " AND quality = '$quality'";
		}

		if (isset($filters['image'])) {
			if ($filters['image'] == 1) {
				$where .= " AND imagepath IS NOT NULL";
			} else {
				$where .= " AND imagepath IS NULL";
			}				
		}

		if (isset($filters['text'])) {
			$text = mysqli_real_escape_string(Persistence::dbConnect(), $filters['text']);
			$where .= " AND text LIKE '%$text%'";
		}

		if (isset($filters['date'])) {
			$date = intval($filters['date']);
			$where .= " AND obsdate <= $date";
		}		
	

		//finally, only pull reports our user is allowed to see
		if (!isset($_SESSION)) session_start();
		
		//logged in, pull public and user's own reports
		if (isset($_SESSION['userid']) && $_SESSION['userid'] != '') {
			$userId = $_SESSION['userid'];
			$where .= " AND (public = '1' OR reporterid = '$userId')";
		} 

		//not logged in, only pull public reports
		else {
			$where .= " AND (public = '1')";
		}

		$sql = $select . $from . $where . $orderby . $limit;

		//var_dump($sql);

		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching reports" . mysqli_error(Persistence::dbConnect()));
		}
		
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$row['id'] = $row['r_id']; //'id' was overwritted by table location table in join	
			$reports[] = $row;
		}

		if(!empty($reports)) return $reports;
		else return NULL;
	}

	public static function getReportbyId($reportId) {
		$reportId = intval($reportId);
		$sql = "SELECT a.id as r_id, b.id as l_id, a.*, b.*, c.* 
				FROM report a 
				INNER JOIN location b ON a.locationid = b.id
				LEFT JOIN sublocation c ON a.sublocationid = c.sl_id 
				WHERE a.id = '$reportId'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching buoys by location" . mysqli_error($link));
		}
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		if (!empty($row)) {
			$row['id'] = $row['r_id'];
			return $row;
		} else return NULL;				
		
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
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error getting user locations" . mysqli_error($link));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$locationInfo[] = $row;	
		}
		return $locationInfo;
	}

	public static function userHasLocation($reporterid, $locationid) {
		$link = Persistence::dbConnect();
		$reporterid = intval($reporterid);
		$locationid = intval($locationid);
		$sql = "SELECT COUNT(*) FROM reporterlocation WHERE reporterid = '$reporterid' AND locationid = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error searching for user-location bookmark" . mysqli_error($link));
		}		
		$row = mysqli_fetch_array($result);	
		if ($row[0] > 0) return TRUE;
		else return FALSE;
	}

	public static function userCreatedLocation($reporterid, $locationid) {
		$link = Persistence::dbConnect();
		$reporterid = intval($reporterid);
		$locationid = intval($locationid);
		$sql = "SELECT COUNT(*) FROM location WHERE creator = '$reporterid' AND id = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error searching for user-location bookmark" . mysqli_error($link));
		}		
		$row = mysqli_fetch_array($result);	
		if ($row[0] > 0) return TRUE;
		else return FALSE;
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

	public static function updateLocationTimezone($locationid, $timezone) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);		
		$timezone = mysqli_real_escape_string($link, $timezone);
		$sql = "UPDATE location SET timezone = '$timezone' WHERE id = '$locationid'";
		$result = mysqli_query($link, $sql) or die("Error updating location timezone");
	}

	public static function updateLocationName($locationid, $name) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);		
		$name = mysqli_real_escape_string($link, $name);
		$sql = "UPDATE location SET locname = '$name' WHERE id = '$locationid'";
		$result = mysqli_query($link, $sql) or die("Error updating location name");
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


	public static function updateLocationForecastUrl($locationid, $oldForecastUrl, $newForecastUrl) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$forecastUrl = mysqli_real_escape_string($link, $forecastUrl);
		$sql = "UPDATE locationforecast SET forecasturl = '$newForecastUrl' WHERE locationid = '$locationid' AND forecasturl = '$oldForecastUrl'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error updating location forecast link in DB" . mysqli_error($link));
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

	public static function getSubLocationInfoById($id) {
		$link = Persistence::dbConnect();
		$id = intval($id);
		$sql = "SELECT * 
				FROM sublocation 
				WHERE sl_id = '$id'";
		$result = mysqli_query($link, $sql);
		$row = mysqli_fetch_object($result);
		return $row;			
	}	
			
		
/*==================================================== Users ====================================================*/
/*===================================================================================================================*/			

	public static function getUsers() {
		$sql = "SELECT id, name, email FROM reporter";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching reporters" . mysqli_error(Persistence::dbConnect()));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$reporters[] = $row;
		}	
		if (!empty($reporters)) {
			return $reporters;
		} else return NULL;				
	}

	public static function insertUser($name = null, $email = null, $password = null, $privacy = null) {
		$link = Persistence::dbConnect();
		$nameStr = isset($name) ? "name = '" . mysqli_real_escape_string($link, $name) . "'" : '';
		$emailStr = isset($email) ? ", email = '" . mysqli_real_escape_string($link, $email) . "'" : '';
		$passwordStr = isset($password) ? ", password = '" . mysqli_real_escape_string($link, $password) . "'" : '';
		$privacyStr = isset($privacy) ? ", public = '" . intval($privacy) . "'" : '';
		$sql = "INSERT INTO reporter SET " . $nameStr . $emailStr . $passwordStr . $privacyStr;
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting reporter into DB" . mysqli_error($link));
		}	
		return mysqli_insert_id($link);	
	}

	public static function deleteUser($reporterId){
		$link = Persistence::dbConnect();
		$id = intval($reporterId);
		$sql = "DELETE FROM reporter WHERE id = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting reporter");
		$sql = "DELETE FROM reporterlocation WHERE reporterid = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting reporter associations");
		$sql = "DELETE FROM report WHERE reporterid = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting reporter report associations");
	}

	public static function insertUserLocation($reporterid, $locationid) {
		$link = Persistence::dbConnect();
		$reporterid = intval($reporterid);
		$locationid = intval($locationid);
		$sql = "INSERT INTO reporterlocation SET reporterid = '$reporterid', locationid = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting location bookmark into DB" . mysqli_error($link));
		}		
	}

	public static function getUserInfoById($id) {
		$id = intval($id);
		$sql = "SELECT * FROM reporter WHERE id = '$id'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching reporter by ID" . mysqli_error(Persistence::dbConnect()));
		}
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		if (!empty($row)) {
			return $row;
		} else return NULL;		
	}

	public static function getUsersByLocation($locid) {
		$locid = intval($locid);
		$sql = "SELECT reporterid FROM reporterlocation WHERE locationid = '$locid'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching reporter by ID" . mysqli_error(Persistence::dbConnect()));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {
			$reporters[] = $row;
		}
		return $reporters;
	}

	public static function updateUserInfo($reporterid, $options = array()) {
		$link = Persistence::dbConnect();
		$reporterid = intval($reporterid);
		$newNameStr = '';
		$newEmailStr = '';
		$newPasswordStr = '';

		if (isset($options['newName'])) {
			$newName = mysqli_real_escape_string($link, $options['newName']);
			$str[] = "name = '$newName'";
		}
		if (isset($options['newEmail'])) {
			$newEmail = mysqli_real_escape_string($link, $options['newEmail']);
			$str[] = "email = '$newEmail'";
		}	
		if (isset($options['privacySetting'])) {
			$privacySetting = intval($options['privacySetting']);
			$str[] = "public = '$privacySetting'";
		}			
		if (isset($options['newPassword'])) {
			$newPassword = mysqli_real_escape_string($link, $options['newPassword']);
			$str[] = "password = '$newPassword'";
		}

		$set = "";
		foreach ($str as $key=>$val) {
			$set .= $val;
			if (isset($str[$key+1])) {
				$set .= ", ";
			}
		}

		$sql = "UPDATE reporter SET " . $set . " WHERE id = '$reporterid'";
		$result = mysqli_query($link, $sql) or print("Error updating user account" . mysqli_error($link));
	}

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
		
	public static function returnUserName($email) {
		$link = Persistence::dbConnect();
		$email = mysqli_real_escape_string($link, $email);
		$sql = "SELECT name FROM reporter WHERE email='$email'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching reporter name");
		}
		$row = mysqli_fetch_array($result);
		return $row['name'];	
	}
	
	public static function returnUserId($username, $password = NULL) {
		$link = Persistence::dbConnect();
		$username = mysqli_real_escape_string($link, $username);
		$sql = "SELECT id FROM reporter WHERE name='$username'";
		if (isset($password)) {
			$password = mysqli_real_escape_string($link, $password);
			$sql .= " AND password='$password'";
		}
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
		
	public static function returnUserEmail($id) {
		$link = Persistence::dbConnect();
		$id = intval($id);
		$sql = "SELECT email FROM reporter WHERE id='$id'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching reporter email");
		}
		$row = mysqli_fetch_array($result);
		return $row['email'];	
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
	
	public static function removeSingleUserCookie($userId, $userKey) {
		$link = Persistence::dbConnect();
		$userId = intval($userId);
		$sql = "DELETE FROM usercookie WHERE userid = '$userId' AND userkey = '$userKey'";
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

/*==================================================== Buoy ====================================================*/
/*==============================================================================================================*/	

	public static function insertBuoy($id, $name = NULL, $locationid = null, $buoynum = null, $checkDbForBuoy = TRUE) {
		$link = Persistence::dbConnect();
		$id = mysqli_real_escape_string($link, $id);
		if ($checkDbForBuoy) {
			if (!Persistence::dbContainsBuoy($id)) {
		
				if (!empty($name)) {
					$name = mysqli_real_escape_string($link, $name);
					$nameSql = ", name = '" . $name . "'";
				} else $nameSql = "";
				
				$sqlbuoy = "INSERT INTO buoy SET buoyid = '$id'" . $nameSql;
				$result = mysqli_query($link, $sqlbuoy);
				if (!$result) {
					die("Error inserting buoy into buoy table" . mysqli_error($link));
				}
			}					
		}

		/* this buoy is associated with a location */
		if (isset($locationid)) {
			$locationid = intval($locationid);		
			$buoynum = intval($buoynum);
			$field = "buoy" . $buoynum;
			$sqllocation = "UPDATE location SET $field = '$id' WHERE id = '$locationid'";			
			$result = mysqli_query($link, $sqllocation);
			if (!$result) {
				die("Error inserting buoy into location table" . mysqli_error($link));
			}
		}
	}

	
	public static function getBuoyData($reportid) {
		$reportid = intval($reportid);
		$sql = "SELECT buoy, gmttime, winddir, windspeed, swellheight, swellperiod, swelldir, tide FROM buoydata WHERE reportid = '$reportid'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching buoydata from db" . mysqli_error(Persistence::dbConnect()));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$buoydatarows[] = $row;
		}
		if(!empty($buoydatarows)) return $buoydatarows;
		else return NULL;
	}

	public static function getAllStations($station, $limit = 100) {
		$link = Persistence::dbConnect();
		$station =  mysqli_real_escape_string($link, $station);
		$limit = intval($limit);
		$sql = "SELECT * FROM $station LIMIT 0 , $limit";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching stations" . mysqli_error($link));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$stations[] = $row;
		}
		return $stations;
	}

	public static function getBuoyInfo($buoy) {
		$link = Persistence::dbConnect();
		$buoy =  mysqli_real_escape_string($link, $buoy);
		$sql = "SELECT * FROM buoy WHERE buoyid = '$buoy'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching buoys by location" . mysqli_error($link));
		}
		return mysqli_fetch_array($result, MYSQL_ASSOC);
	}


	public static function setPrimaryBuoy($locationid, $buoy) {
		$locInfo = Persistence::getLocationInfoById($locationid);
		$link = Persistence::dbConnect();
		$buoy = mysqli_real_escape_string($link, $buoy);
		$buoy1 = $locInfo['buoy1'];
		if ($buoy == $locInfo['buoy2']) {
			$replace = "buoy2 = '$buoy1'";
		} else {
			$replace = "buoy3 = '$buoy1'";
		}
		$buoy3 = $locInfo['buoy3'];
		$sql = "UPDATE location SET buoy1 = '$buoy', " . $replace . " WHERE id = '$locationid'";
			//vardump($sql);exit();
	
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error making Buoy primary" . mysqli_error($link));
		}		
	}

	public static function removeBuoyFromLocation($key, $buoys, $locationid) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$key = mysqli_real_escape_string($link, $key);
		$switch = '';

		//this is crazy - should of made a seperate table for buoy-location relationships
		if ($key == 'buoy3') {
			$switch = "buoy3 = NULL";
		}
		
		if ($key == 'buoy2' && isset($buoys['buoy3'])) {
			$buoy3 = $buoys['buoy3'];
			$switch = "buoy2 = '$buoy3', buoy3 = NULL";
		} else if ($key == 'buoy2' && !isset($buoys['buoy3'])) {
			$switch = "buoy2 = NULL";
		}
		
		if ($key == 'buoy1' && isset($buoys['buoy2']) && isset($buoys['buoy3'])) {
			$buoy2 = $buoys['buoy2'];
			$buoy3 = $buoys['buoy3'];
			$switch = "buoy1 = '$buoy2', buoy2 = '$buoy3', buoy3 = NULL";
		} else if ($key == 'buoy1' && isset($buoys['buoy2'])) {
			$buoy2 = $buoys['buoy2'];
			$switch = "buoy1 = '$buoy2', buoy2 = NULL";
		} else if ($key == 'buoy1') {
			$switch = "buoy1 = NULL";
		}

		$sql = "UPDATE location SET " . $switch . " WHERE id = '$locationid'";
		$result = mysqli_query($link, $sql) or die ('unable to remove buoy');
	}

	public static function dbContainsBuoy($buoyid) {
		$link = Persistence::dbConnect();
		$buoyid = mysqli_real_escape_string($link, $buoyid);
		$sql = "SELECT COUNT(*) FROM buoy WHERE buoyid = '$buoyid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error searching for buoys in db");
		}
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function deleteBuoy($id) {
		$link = Persistence::dbConnect();
		$id = mysqli_real_escape_string($link, $id);
		$sql = "DELETE FROM buoy WHERE buoyid = '$id'";
		$result = mysqli_query($link, $sql) or die("Error deleting buoy");
	}


	public static function updateBuoy($oldId, $id, $name) {
		$link = Persistence::dbConnect();
		$id = mysqli_real_escape_string($link, $id);
		$name = mysqli_real_escape_string($link, $name);
		$sql = "UPDATE buoy SET buoyid = '$id', name = '$name' WHERE buoyid = '$oldId'";
		$result = mysqli_query($link, $sql) or die("Error updating buoy");
	}	

/*==================================================== Tide stations ====================================================*/
/*========================================================================================================================*/	

	public static function insertTideStation($stationid, $stationname, $locationid, $checkDbForStation = TRUE) {
		$link = Persistence::dbConnect();
		$stationid = mysqli_real_escape_string($link, $stationid);
		$stationname = mysqli_real_escape_string($link, $stationname);	
		
		if ($checkDbForStation) {
			if (!Persistence::dbContainsTideStation($stationid)) {		
				if (!empty($stationname)) {
					$stationname = mysqli_real_escape_string($link, $stationname);
					$nameSql = ", stationname = '" . $stationname . "'";
				} else $nameSql = "";
				
				$sql = "INSERT INTO tidestation SET stationid = '$stationid'" . $nameSql;
				$result = mysqli_query($link, $sql);
				if (!$result) {
					die("Error inserting station into tide station table" . mysqli_error($link));
				}
			}	
		}
				
		$locationid = intval($locationid);
		$sql = "UPDATE location SET tidestation = '$stationid' WHERE id = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting tide station into location table" . mysqli_error($link));
		}
	}

	public static function getTideData($reportid) {
		$reportid = intval($reportid);
		$sql = "SELECT tide, tidedate, tideres FROM tidedata WHERE reportid = '$reportid'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching tidedata from db" . mysqli_error(Persistence::dbConnect()));
		}
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);	
		if (!empty($row)) return $row;
		else return NULL;
	}
			
	public static function getTideStationInfo($stationid) {
		$link = Persistence::dbConnect();
		$stationid = mysqli_real_escape_string($link, $stationid);
		$sql = "SELECT * FROM tidestation WHERE stationid = '$stationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching buoys by location" . mysqli_error($link));
		}
		return mysqli_fetch_array($result, MYSQL_ASSOC);
	}

	public static function removeTideStationFromLocation($station, $locationid) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$station = mysqli_real_escape_string($link, $station);		
		$sql = "UPDATE location SET tidestation = NULL WHERE id = '$locationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error removing tide station from location");
		}
	}

	public static function dbContainsTideStation($stationid) {
		$link = Persistence::dbConnect();
		$stationid = mysqli_real_escape_string($link, $stationid);
		$sql = "SELECT COUNT(*) FROM tidestation WHERE stationid = '$stationid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error searching for tide stations in db");
		}
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}


}


?>