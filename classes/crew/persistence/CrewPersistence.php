<?

class CrewPersistence {
	
	public static function insertCrew($crew = array()) {
		
		if (empty($crew)) return;

		$link = Persistence::dbConnect();

		$creator = intval($crew['creator']); //needs to be passed in!

		$nameStr = '';
		if (isset($crew['name'])) {
			$name = mysqli_real_escape_string($link, $crew['name']);
			$nameStr = ", name = '$name'";
		}

		$descriptionStr = '';
		if (isset($crew['description'])) {
			$description = mysqli_real_escape_string($link, $crew['description']);
			$descriptionStr = ", description = '$description'";
		}		

		$sql = "INSERT INTO crew SET creator = '$creator'" . $nameStr . $descriptionStr;
		//var_dump($sql); exit;
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error creating crew");
		}
		return mysqli_insert_id($link);

	}

	public static function getCrewById($crewId) {
		$link = Persistence::dbConnect();
		$crewId = intval($crewId);
		$sql = "SELECT * FROM crew WHERE id = '$crewId'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error fetching crew by id");
		}
		return mysqli_fetch_array($result, MYSQL_ASSOC);	
	}

	public static function getCrewByName($crewName) {
		$link = Persistence::dbConnect();
		$crewName = mysqli_real_escape_string($link, $crewName);
		$sql = "SELECT * FROM crew WHERE name = '$crewName'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error fetching crew by name");
		}
		return mysqli_fetch_array($result, MYSQL_ASSOC);	
	}	

	public static function getUsersByCrew($crewId) {
		$link = Persistence::dbConnect();
		$crewId = intval($crewId);
		$sql = "SELECT * FROM reporter a INNER JOIN reportercrew b ON a.id = b.reporter WHERE b.crew = '$crewId'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error fetching crew members by crewid");
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$reporters[] = $row;
		}
		return $reporters;	
	}		


	public static function insertUserIntoCrew($reporterId, $crewId) {
		$link = Persistence::dbConnect();
		$reporterId = intval($reporterId);
		$crewId = intval($crewId);
		$sql = "INSERT INTO reportercrew SET reporter = '$reporterId', crew = '$crewId'";
		$result = mysqli_query($link, $sql);
		if (!$result) {
			die("error inserting user into crew");
		}
	}

	public static function removeUserFromCrew($user, $crew) {}

	public static function deleteCrew($crew) {}

	public static function updateCrew($crew) {}
}

?>