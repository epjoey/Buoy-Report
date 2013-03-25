<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ReporterService {
	static function getReporter($id, $options = array()) {
		if (!$id) {
			return null;
		}
		$reporters = ReporterPersistence::getReporters(array($id), $options);
		$reporter = reset($reporters);
		return $reporter;
	}

	static function reporterAddLocation($rid, $lid) {
		if (!$rid || !$lid) {
			throw new InternalExcetion();
		}
		if (!ReporterPersistence::reporterHasLocation($rid, $lid)) {
			return ReporterPersistence::reporterAddLocation($rid, $lid);
		}
	}	

	static function getReporterLocationIds($reporter) {
		return ReporterPersistence::getReporterLocationIds($reporter);
	}

}
?>