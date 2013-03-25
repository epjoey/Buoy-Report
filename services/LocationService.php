<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class LocationService {
	
	static function getLocation($id, $options = array()) {
		return reset(self::getLocations(array($id), $options));
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

	public static function getReporterLocations($reporter){
		if (!$reporter) {
			return array();
		}
		$lids = ReporterService::getReporterLocationIds($reporter);
		return self::getLocations($lids);
	}		

	public static function getSublocationsForLocation($location) {
		return LocationPersistence::getSublocationsForLocation($location);
	}

	public static function getSublocation($id) {
		if (!$id) {
			return null;
		}
		$sublocations = LocationPersistence::getSublocations(array($id));
		return reset($sublocations);
	}
}
?>