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

	static function reporterAddLocation($rid, $lid) {
		if (!$rid || !$lid) {
			throw new InternalException();
		}
		if (!ReporterPersistence::reporterHasLocation($rid, $lid)) {
			return ReporterPersistence::reporterAddLocation($rid, $lid);
		}
	}	

	static function getReporterLocationIds($reporter) {
		return ReporterPersistence::getReporterLocationIds($reporter);
	}

	static function getReportersForLocationIds($lids) {
		//$ids = ReporterPersistence::getReporterIdsForLocationIds();
	}
}
?>