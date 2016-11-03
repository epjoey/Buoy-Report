<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LocationService {
	
	static function getLocation($id, $options = array()) {
		$locations = self::getLocations(array($id), $options);
		return reset($locations);
	}

	static function getLocations($ids, $options = array()) {
		$defaultOptions = array(
			'includeSublocations' => false,
			'includeBuoys' => false,
			'includeTideStations' => false
		);
		$options = array_merge($defaultOptions, $options);	
		$locations = LocationPersistence::getLocations($ids);
		
		foreach($locations as $location) {
			if ($options['includeSublocations']) {
				$location->sublocations = self::getSublocationsForLocation($location);
			}
			if ($options['includeBuoys']) {
				$location->buoys = BuoyService::getBuoysForLocation($location);
			}
			if ($options['includeTideStations']) {
				$location->tideStations = TideStationService::getTideStationsForLocation($location);
			}		
		}
		return $locations;
	}

	public static function getReporterLocations($reporter, $options = array()){
		if (!$reporter) {
			return array();
		}
		$lids = ReporterService::getReporterLocationIds($reporter);
		return self::getLocations($lids, $options);
	}		

	public static function getSublocationsForLocation($location) {
		return LocationPersistence::getSublocationsForLocation($location);
	}

	public static function updateLocation($location) {
		LocationPersistence::updateLocation($location);
	}

	public static function getSublocationIdsForLocation($location) {
		return LocationPersistence::getSublocationIdsForLocation($location->id);
	}

	static function getAllLocations($options = array()) {
		$ids = self::getAllLocationIds($options);
		return self::getLocations($ids, $options);
	}

	static function getAllLocationIds($options = array()) {
		return LocationPersistence::getAllLocationIds($options);
	}	
}
?>