<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';

class LocationPersistence {
	public static function getLocations($options = array()) {
		$locations = array();
		$defaultOptions = array(
			'limit' => 50,
			'order' => 'locname ASC'
		);
		$options = array_merge($defaultOptions, $options);
		
		$link = Persistence::dbConnect();
		$order = mysqli_real_escape_string($link, $options['order']);
		$limit = intval($options['limit']);

		$sql = "SELECT * 
				FROM location 
				ORDER BY $order 
				LIMIT $limit";
		
		$result = mysqli_query($link, $sql);
		
		if (!$result) {
			die("Error fetching locations" . mysqli_error($link));
		}
		while ($row = mysqli_fetch_array($result, MYSQL_ASSOC)) {	
			$locations[] = $row;
		}
		return $locations;
	}		
}
?>