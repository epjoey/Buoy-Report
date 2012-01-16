<?php

class Persistence {
	public static function dbConnect() {

		global $local_dev;
		
		$host = 'mysql';
		$db = 'br';
		$un = 'root';
		$pw = 'ivytila';
		
		if ($local_dev) {
			$db = 'reportdb';
			$host = 'localhost';
			$un = 'root';
			$pw = 'joeyho99';
		}
					
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

		return $link;				
	}

/*==================================================== General Reports ====================================================*/
/*=========================================================================================================================*/
 
	public static function insertReport($reportInfo = array()) {
		//to do:create $fields
		$link = Persistence::dbConnect();
		$locationid = intval($reportInfo['locId']);
		$reporterid = intval($reportInfo['reporterId']);
		$obsdate = intval($reportInfo['observationDate']);
		$reportdate = intval($reportInfo['reportDate']);
		$fields = "locationid = '$locationid', reporterid = '$reporterid', obsdate = '$obsdate', reportdate = '$reportdate'";
		if (isset($reportInfo['quality'])) {
			$quality = mysqli_real_escape_string($link, $reportInfo['quality']);
			$fields .= ", quality = '" . $quality . "'";
		}
		if (isset($reportInfo['text'])) {
			$text = mysqli_real_escape_string($link, $reportInfo['text']);
			$fields .= ", text = '" . $text . "'";			
		}
		if (isset($reportInfo['imagepath'])) {
			$imagepath = mysqli_real_escape_string($link, $reportInfo['imagepath']);
			$fields .= ", imagepath = '" . $imagepath . "'";			
		}		
		$sql = "INSERT INTO report SET $fields";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting report into DB" . mysqli_error($link));
		}
		$reportid = mysqli_insert_id($link);
		if (Persistence::userHasLocation($reporterid, $locationid) == FALSE) {
			Persistence::insertUserLocation($reporterid, $locationid);		
		}
		return $reportid;
	}	

	public static function getReports($filters = array(), $limit = 6) {		
		//the basic SELECT statement
		$select = 'SELECT *';
		$from = ' FROM report';
		$where = ' WHERE TRUE';
		$orderby = ' ORDER BY obsdate DESC';
		$limit = ' LIMIT ' . $limit;

		if (!empty($filters['reporters'])) {
			$where .= " AND (";
			foreach ($filters['reporters'] as $key=>$reporter) {
				$where .= " reporterid = '$reporter'";
				if(isset($filters['reporters'][$key+1])) {
					$where .= " OR";
				}
			}
			$where .= ") "; 			
		}

		if (!empty($filters['locations'])) {
			$where .= " AND (";
			foreach ($filters['locations'] as $key=>$location) {
				$where .= " locationid = '$location'";
				if(isset($filters['locations'][$key+1])) {
					$where .= " OR";
				}
			}
			$where .= ") "; 			
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
	

		$sql = $select . $from . $where . $orderby . $limit;

		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching reports" . mysqli_error(Persistence::dbConnect()));
		}
		
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$reports[] = $row;
		}

		if(!empty($reports)) return $reports;
		else return NULL;
	}

	public static function getReportbyId($reportId) {
		$reportId = intval($reportId);
		$sql = "SELECT * FROM report WHERE id = '$reportId'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching bouys by location" . mysqli_error($link));
		}
		$row = mysqli_fetch_array($result, MYSQL_ASSOC);
		if (!empty($row)) {
			return $row;
		} else return NULL;				
		
	}

	public static function updateReport($reportInfo = array()) {
		$link = Persistence::dbConnect();
		$reportId = intval($reportInfo['id']);
		
		$quality = mysqli_real_escape_string($link, $reportInfo['quality']);
		$fields = " quality = '" . $quality . "'";

		if (!empty($reportInfo['text'])) {
			$text = mysqli_real_escape_string($link, $reportInfo['text']);
			$fields .= ", text = '" . $text . "'";			
		} else $fields .= ", text = NULL";

		if (isset($reportInfo['imagepath'])) {

			if ($reportInfo['imagepath'] != '') {
				$imagepath = mysqli_real_escape_string($link, $reportInfo['imagepath']);
				$fields .= ", imagepath = '" . $imagepath . "'";					
			} else {
				$fields .= ", imagepath = NULL";
			}
		}
		$sql = "UPDATE report SET $fields WHERE id = '$reportId'";	
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error updating report" . mysqli_error($link));
		}		
		
	}

	public static function deleteReport($reportId) {
		$reportId = intval($reportId);
		$sql = "DELETE FROM report WHERE id = '$reportId'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error deleting report" . mysqli_error($link));
		}
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
			die("Error fetching bouys by location" . mysqli_error($link));
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
		
/*==================================================== Reporters ====================================================*/
/*===================================================================================================================*/			

	public static function getReporters() {
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

	public static function insertReporter($name, $email, $password) {
		$link = Persistence::dbConnect();
		isset($name) ? $name = mysqli_real_escape_string($link, $name) : $name=NULL;
		$email = mysqli_real_escape_string($link, $email);	
		$password = mysqli_real_escape_string($link, $password);
		$sql = "INSERT INTO reporter SET name = '$name', email = '$email', password = '$password'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting reporter into DB" . mysqli_error($link));
		}	
		return mysqli_insert_id($link);	
	}

	public static function deleteReporter($reporterId){
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

	public static function getReporterInfoById($id) {
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

	public static function getReportersByLocation($locid) {
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
			$newNameStr = "name = '$newName'";
		}
		if (isset($options['newEmail'])) {
			$newEmail = mysqli_real_escape_string($link, $options['newEmail']);
			$newEmailStr = "email = '$newEmail'";
			if ($newNameStr != "") $newNameStr .= ", ";
		}		
		if (isset($options['newPassword'])) {
			$newPassword = mysqli_real_escape_string($link, $options['newPassword']);
			$newPasswordStr = "password = '$newPassword'";
			if ($newEmailStr != "") {
				$newEmailStr .= ", ";
			} else if ($newNameStr != "") {
				$newNameStr .= ", ";
			}
		}

		$sql = "UPDATE reporter SET " . $newNameStr . $newEmailStr . $newPasswordStr . " WHERE id = '$reporterid'";
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
		
	public static function returnReporterName($email) {
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
	
	public static function returnReporterId($email, $password = NULL) {
		$link = Persistence::dbConnect();
		$email = mysqli_real_escape_string($link, $email);
		$sql = "SELECT id FROM reporter WHERE email='$email'";
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
		
	public static function returnReporterEmail($id) {
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

/*==================================================== Bouy ====================================================*/
/*==============================================================================================================*/	

	public static function insertBouy($id, $name = NULL, $locationid, $bouynum, $checkDbForBouy = TRUE) {
		$link = Persistence::dbConnect();
		$id = mysqli_real_escape_string($link, $id);
		if ($checkDbForBouy) {
			if (!Persistence::dbContainsBouy($id)) {
		
				if (!empty($name)) {
					$name = mysqli_real_escape_string($link, $name);
					$nameSql = ", name = '" . $name . "'";
				} else $nameSql = "";
				
				$sqlbouy = "INSERT INTO bouy SET bouyid = '$id'" . $nameSql;
				$result = mysqli_query($link, $sqlbouy);
				if (!$result) {
					die("Error inserting bouy into bouy table" . mysqli_error($link));
				}
			}					
		}
		$locationid = intval($locationid);	
		$bouynum = intval($bouynum);
		$field = "bouy" . $bouynum;
		$sqllocation = "UPDATE location SET $field = '$id' WHERE id = '$locationid'";		
		$result = mysqli_query($link, $sqllocation);
		if (!$result) {
			die("Error inserting bouy into location table" . mysqli_error($link));
		}
	}

	public static function insertBouyData($reportid, $fields = array()) {
		$link = Persistence::dbConnect();
		$reportid = intval($reportid);
		$set = "reportid = '$reportid'";
		foreach ($fields as $fieldk => $fieldv ) {
			if ($fieldv != "" && $fieldv != "MM") {
				$fieldv = mysqli_real_escape_string($link, $fieldv);
				$set .= ", " . $fieldk . " = '" . $fieldv . "'";		
			}
		} 

		$sql = "INSERT INTO bouydata SET $set";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting bouydata into DB" . mysqli_error($link));
		}		
	}
	
	public static function getBouyData($reportid) {
		$reportid = intval($reportid);
		$sql = "SELECT bouy, gmttime, winddir, windspeed, swellheight, swellperiod, swelldir, tide FROM bouydata WHERE reportid = '$reportid'";
		$result = mysqli_query(Persistence::dbConnect(), $sql);
		if (!$result) {
			die("Error fetching bouydata from db" . mysqli_error(Persistence::dbConnect()));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$bouydatarows[] = $row;
		}
		if(!empty($bouydatarows)) return $bouydatarows;
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

	public static function getBouyInfo($bouy) {
		$link = Persistence::dbConnect();
		$bouy =  mysqli_real_escape_string($link, $bouy);
		$sql = "SELECT * FROM bouy WHERE bouyid = '$bouy'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error fetching bouys by location" . mysqli_error($link));
		}
		return mysqli_fetch_array($result, MYSQL_ASSOC);
	}


	public static function setPrimaryBouy($locationid, $bouy) {
		$locInfo = Persistence::getLocationInfoById($locationid);
		$link = Persistence::dbConnect();
		$bouy = mysqli_real_escape_string($link, $bouy);
		$bouy1 = $locInfo['bouy1'];
		if ($bouy == $locInfo['bouy2']) {
			$replace = "bouy2 = '$bouy1'";
		} else {
			$replace = "bouy3 = '$bouy1'";
		}
		$bouy3 = $locInfo['bouy3'];
		$sql = "UPDATE location SET bouy1 = '$bouy', " . $replace . " WHERE id = '$locationid'";
			//vardump($sql);exit();
	
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error making Bouy primary" . mysqli_error($link));
		}		
	}

	public static function removeBouyFromLocation($key, $bouys, $locationid) {
		$link = Persistence::dbConnect();
		$locationid = intval($locationid);
		$key = mysqli_real_escape_string($link, $key);
		$switch = '';

		//this is crazy - should of made a seperate table for bouy-location relationships
		if ($key == 'bouy3') {
			$switch = "bouy3 = NULL";
		}
		
		if ($key == 'bouy2' && isset($bouys['bouy3'])) {
			$bouy3 = $bouys['bouy3'];
			$switch = "bouy2 = '$bouy3', bouy3 = NULL";
		} else if ($key == 'bouy2' && !isset($bouys['bouy3'])) {
			$switch = "bouy2 = NULL";
		}
		
		if ($key == 'bouy1' && isset($bouys['bouy2']) && isset($bouys['bouy3'])) {
			$bouy2 = $bouys['bouy2'];
			$bouy3 = $bouys['bouy3'];
			$switch = "bouy1 = '$bouy2', bouy2 = '$bouy3', bouy3 = NULL";
		} else if ($key == 'bouy1' && isset($bouys['bouy2'])) {
			$bouy2 = $bouys['bouy2'];
			$switch = "bouy1 = '$bouy2', bouy2 = NULL";
		} else if ($key == 'bouy1') {
			$switch = "bouy1 = NULL";
		}

		$sql = "UPDATE location SET " . $switch . " WHERE id = '$locationid'";
		$result = mysqli_query($link, $sql) or die ('unable to remove bouy');
	}

	public static function dbContainsBouy($bouyid) {
		$link = Persistence::dbConnect();
		$bouyid = mysqli_real_escape_string($link, $bouyid);
		$sql = "SELECT COUNT(*) FROM bouy WHERE bouyid = '$bouyid'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error searching for bouys in db");
		}
		$row = mysqli_fetch_array($result);
		if ($row[0] > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
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

	public static function insertTideData($reportid, $tide, $res, $date) {
		$link = Persistence::dbConnect();
		$reportid = intval($reportid);
		$date = intval($date);
		$sql = "INSERT INTO tidedata SET reportid = '$reportid', tide = '$tide', tideres = '$res', tidedate = '$date'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("Error inserting tidedata into DB" . mysqli_error($link));
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
			die("Error fetching bouys by location" . mysqli_error($link));
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