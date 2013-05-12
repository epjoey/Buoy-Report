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

	static function getTideReportsFromStationsForReport($tideStationIds, $report, $options = array()) {
		$defaultOptions = array(
			'stationType' => 'NOAA'
		);
		$options = array_merge($defaultOptions, $options);
		$tideReports = array();
		foreach($tideStationIds as $tideStationId) {
			try {
				$tideReport = NOAATidePersistence::getTideReportFromStationAtTime($tideStationId, $report->obsdate);
			} catch (NOAATideReportException $e) {
				continue;
			}
			$tideReports[] = new TideReport(array_merge($tideReport, array(
				'tidestation' => $tideStationId,
				'reportid' => $report->id
			)));
		}
		return $tideReports;
	}

	static function getAndSaveTideReportsForReport($report, $tideStationIds) {
		$tideReports = self::getTideReportsFromStationsForReport($tideStationIds, $report);
		self::saveTideReportsForReport($tideReports);
	}

	static function saveTideReportsForReport($tideReports) {
		foreach ($tideReports as $tideReport) {
			TideReportPersistence::insertTideReport($tideReport);	
		}
	}



}



?>