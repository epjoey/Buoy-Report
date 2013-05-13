<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideReportService {

	static function getTideReportsForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeTideStationModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$tideReports = TideReportPersistence::getTideReportsForReport($report);
		if ($options['includeTideStationModel']) {
			foreach($tideReports as $tideReport) {
				$tideReport->tideStationModel = TideStationService::getTideStation($tideReport->tidestation);
			}
		}
		return $tideReports;
	}

	static function getTideStationTideReport($tideStation, $options = array()) {
		try {
			$tideReport = NOAATidePersistence::getTideStationTideReport($tideStation, $options);
		} catch (NOAATideReportException $e) {
			error_log("Tide station $tideStation->stationid failure - ". $e->getMessage());
			return null;
		}
		return $tideReport;
	}

	static function insertTideReport($tideReport) {
		TideReportPersistence::insertTideReport($tideReport);
	}
}
?>