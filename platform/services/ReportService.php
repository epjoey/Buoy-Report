<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class ReportService {

	static function updateReport($newReport) {
		ReportPersistence::updateReport($newReport);
	}	
	static function getReport($id, $options = array()) {
		$reports = self::getReports(array($id), $options);		
		return reset($reports);
	}	

	static function getReports($ids, $options = array()) {
		$defaultOptions = array(
			'includeBuoyReports' => false,
			'includeTideReports' => false,
			'includeLocation' => false,
			'includeBuoyModel' => false,
			'includeTideStationModel' => false,
			'includeReporter' => false,
		);
		$options = array_merge($defaultOptions, $options);		
		$reports = ReportPersistence::getReports($ids);
		foreach($reports as $report) {
			if ($options['includeBuoyReports']) {
				$report->buoyReports = BuoyReportService::getBuoyReportsForReport($report, array(
					'includeBuoyModel' => $options['includeBuoyModel']
				));
			}
			if ($options['includeTideReports']) {
				$report->tideReports = TideReportService::getTideReportsForReport($report, array(
					'includeTideStationModel' => $options['includeTideStationModel']
				));
			}
			if ($options['includeLocation']) {
				$report->location = LocationService::getLocation($report->locationid);
			}
			if ($options['includeReporter']) {
				$report->reporter = ReporterService::getReporter($report->reporterid);
			}
		}	
		return $reports;		
	}


	static function insertReport($options = array()) {
		// if (!$options['quality']) {
		// 	throw new InvalidSubmissionException('You must choose a quality.');
		// }
		if (!$options['obsdate']) {
			throw new InvalidSubmissionException('No time entered');
		}
		
		return ReportPersistence::insertReport($options);	
	}


	static function getReportsForUserWithFilters($user, $filters, $options = array()) {
		$ids = ReportPersistence::getReportIdsForUserWithFilters($user, $filters, $options);
		//temporary inefficient loop
		return self::getReports($ids, array(
			'includeBuoyReports' => true,
			'includeTideReports' => true,
			'includeLocation' => true,
			'includeBuoyModel' => true,
			'includeTideStationModel' => true,
			'includeReporter' => true,
		));
	}

	static function deleteReport($id) {
		ReportPersistence::deleteReport($id);
	}
}
?>