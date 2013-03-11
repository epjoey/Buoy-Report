<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/reporter/persistence/ReporterPersistence.php';

class ReporterService {
	static function getReporter($id, $options = array()) {
		if (!$id) {
			return null;
		}
		$reporters = ReporterPersistence::getReporters(array($id), $options);
		$reporter = reset($reporters);
		return $reporter;
	}

}
?>