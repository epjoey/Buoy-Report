<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/location/persistence/LocationPersistence.php';

class LocationService {

	/* this should go in a UserLocation class */
	public static function getUserLocations($user){
	
		//logged out user -- return all locations (TODO:return only recently active locations)
		if (!$user->isLoggedIn) {
			return array(); //LocationPersistence::getLocations();
		}

		$locations = Persistence::getUserLocations($user->id);
		if (!$locations) {
			return array();
		}

		/* 
		 * Squish the new report info into 
		 * the locations array before new 
		 * report is submitted into DB.
		 * 
		 */
		if ($user->hasNewReport) {

			if ($user->newReport['reporterHasLocation'] == '0') {
				array_unshift(
					$locations, 
					array(
						'id'=>$user->newReport['locationid'], 
						'locname'=>$user->newReport['locationname']
					)
				);
			}
		}

		return $locations;
	}		
}


?>