<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ReporterService {
	static function getReporter($id, $options = array()) {
		if (!$id) {
			return null;
		}
		return reset(self::getReporters(array($id), $options));
	}

	static function getReporters($ids, $options = array()) {
		$reporters = ReporterPersistence::getReporters($ids);
		foreach($reporters as $reporter) {
			if ($options['includeLocations']) {
				$reporter->locations = LocationService::getReporterLocations($reporter);	
			}
		}
		return $reporters;
		
	}

	static function getAllReporters() {
		$ids = ReporterPersistence::getAllReporterIds();
		return self::getReporters($ids);
	}

	static function reporterAddLocation($rid, $lid) {
		if (!$rid || !$lid) {
			throw new InvalidArgumentException();
		}
		if (!ReporterPersistence::reporterHasLocation($rid, $lid)) {
			return ReporterPersistence::reporterAddLocation($rid, $lid);
		}
	}	

	static function getReporterLocationIds($reporter) {
		return ReporterPersistence::getReporterLocationIds($reporter);
	}

	static function getLocationReporters($location) {
		$reporterIds = ReporterPersistence::getReporterIdsForLocation($location);
		return self::getReporters($reporterIds);
	}

	static function createReporter($name, $email, $password, $options = array()) {
		$id = ReporterPersistence::createReporter($name, $email, $password, $options);
		return self::getReporter($id);
	}

	static function updateReporter($reporter, $properties = array()) {
		ReporterPersistence::updateReporter($reporter, $properties);
	}

	static function deleteReporter($reporter) {
		ReporterPersistence::deleteReporter($reporter);
	}

	static function getReporterByUsernameAndPassword($name, $pw) {
		$id = Persistence::returnUserId($name, $pw);
		return self::getReporter($id);
	}
}
?>