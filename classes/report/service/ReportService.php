<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/report/persistence/ReportPersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/service/BuoyDataService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidedata/service/TideDataService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/reporter/service/ReporterService.php';

class ReportService {

	static function updateReport($newReport) {
		ReportPersistence::updateReport($newReport);
	}	
	static function getReport($id, $options = array()) {
		$defaultOptions = array(
			'includeBuoyData' => false,
			'includeTideData' => false,
			'includeLocation' => false,
			'includeBuoyModel' => false,
			'includeTideStationModel' => false,
			'includeReporter' => false
		);
		$options = array_merge($defaultOptions, $options);
		$reports = ReportPersistence::getReports(array($id));		
		$report = reset($reports);

		if ($options['includeBuoyData']) {
			$report->buoyData = BuoyDataService::getSavedBuoyDataForReport($report, array(
				'includeBuoyModel' => $options['includeBuoyModel']
			));
		}
		if ($options['includeTideData']) {
			$report->tideData = TideDataService::getSavedTideDataForReport($report, array(
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
		return $report;
	}	

	static function getReports($ids) {
		return ReportPersistence::getReports($ids);
	}

	static function saveReport($report) {
		if (!$report->quality) {
			throw new Exception('no-quality');
		}
		if (!$report->obsdate) {
			throw new Exception('no-time');
		}			
		$report->reportdate = intval(gmdate("U")); //time of report (now)

		$reportId = ReportPersistence::insertReport($report);	
		return $reportId;
	}

	static function getReportsForUserWithFilters($user, $filters, $options = array()) {
		$ids = ReportPersistence::getReportIdsForUserWithFilters($user, $filters, $options);
		
		//temporary inefficient loop
		$reports = array();
		foreach($ids as $id) {
			$reports[] = self::getReport($id, array(
				'includeBuoyData' => true,
				'includeTideData' => true,
				'includeLocation' => true,
				'includeBuoyModel' => true,
				'includeTideStationModel' => true,
				'includeReporter' => true
			));
		}
		//$reports = self::getReports($ids);
		return $reports;
	}

	static function deleteReport($id) {
		ReportPersistence::deleteReport($id);
	}
}
?>