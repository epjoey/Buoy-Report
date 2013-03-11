<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location/persistence/LocationPersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/service/TideStationService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';

class LocationService {
	static function getLocation($id, $options = array()) {
		if (!$id) {
			return null;
		}
		$defaultOptions = array(
			'includeSublocations' => false,
			'includeBuoys' => false,
			'includeTideStations' => false
		);
		$options = array_merge($defaultOptions, $options);
		$id = intval($id);
		$locations = LocationPersistence::getLocations(array($id));
		$location = reset($locations);

		if ($options['includeSublocations']) {
			$location->sublocations = self::getSublocationsForLocation($location);
		}
		if ($options['includeBuoys']) {
			$location->buoys = BuoyService::getBuoysForLocation($location);
		}
		if ($options['includeTideStations']) {
			$location->tideStations = TideStationService::getTideStationsForLocation($location);
		}
		return $location;
	}

	static function getLocations($ids) {
		return LocationPersistence::getLocations($ids);
	}

	public static function getUserLocations($user){
		//logged out user -- return all locations (TODO:return only recently active locations)
		if (!$user->isLoggedIn) {
			return array(); //LocationPersistence::getLocations();
		}
		$locations = Persistence::getUserLocations($user->id);
		if (!$locations) {
			return array();
		}
		return $locations;
	}		

	public static function getSublocationsForLocation($location) {
		return LocationPersistence::getSublocationsForLocation($location);
	}

	public static function getSublocation($id) {
		if (!$id) {
			return null;
		}
		$sublocations = LocationPersistence::getLocations(array($id));
		return reset($sublocations);
	}
}
?>