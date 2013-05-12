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
			'includeSublocation' => false
		);
		$options = array_merge($defaultOptions, $options);		
		$reports = ReportPersistence::getReports($ids);
		foreach($reports as $report) {
			if ($options['includeBuoyReports']) {
				$report->buoyReports = BuoyReportService::getSavedBuoyReportsForReport($report, array(
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
			if ($options['includeSublocation']) {
				$report->sublocation = LocationService::getSublocation($report->sublocationid);
			}	
			if ($options['includeReporter']) {
				$report->reporter = ReporterService::getReporter($report->reporterid);
			}
		}	
		return $reports;		
	}


	/* big one */
	static function saveReport($report, $options = array()) {
		if (!$report->quality) {
			throw new InvalidSubmissionException('You must choose a quality.');
		}
		if (!$report->obsdate) {
			throw new InvalidSubmissionException('No time entered');
		}			
		$report->reportdate = intval(gmdate("U")); //time of report (now)
		
		$id = ReportPersistence::insertReport($report);	

		$report = self::getReport($id);
		
		if ($options['tidestationIds']) {
			TideReportService::getAndSaveTideReportsForReport($report, $options['tidestationIds']);
		}

		if ($options['buoyIds']) {
			BuoyReportService::getAndSaveBuoyReportsForReport($report, $options['buoyIds']);
		}

		ReporterService::reporterAddLocation($report->reporterid, $report->locationid);

		return $report;
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
			'includeSublocation' => true
		));
	}

	static function deleteReport($id) {
		ReportPersistence::deleteReport($id);
	}
}
?>