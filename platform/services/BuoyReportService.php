<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyReportService {

	static function getSavedBuoyReportsForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeBuoyModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$buoyReports = BuoyReportPersistence::getSavedBuoyReportsForReport($report);
		if ($options['includeBuoyModel']) {
			foreach($buoyReports as $buoyReport) {
				$buoyReport->buoyModel = BuoyService::getBuoy($buoyReport->buoy);
			}
		}
		return $buoyReports;
	}

	static function getBuoyReportsFromBuoysForReport($buoyIds, $report, $options = array()) {
		$defaultOptions = array(
			'stationType' => 'NOAA'
		);
		$options = array_merge($defaultOptions, $options);
		$data = array();
		foreach($buoyIds as $buoyId) {
			try {
				$buoyReport = NOAABuoyPersistence::getBuoyReportFromBuoyAtTime($buoyId, $report->obsdate);
			} catch (NOAABuoyException $e) {
				error_log("$buoyId error");
				continue;
			}
			if ($buoyReport instanceof BuoyReport) {
				$buoyReport->buoy = $buoyId;
				$buoyReport->reportid = $report->id;
				$data[] = $buoyReport;
			}			
		}
		return $data;
	}

	static function getAndSaveBuoyReportsForReport($report, $buoyIds) {
		$buoyReports = self::getBuoyReportsFromBuoysForReport($buoyIds, $report);
		self::saveBuoyReportsForReport($buoyReports);
	}

	static function saveBuoyReportsForReport($buoyReports) {
		foreach ($buoyReports as $buoyReport) {
			BuoyReportPersistence::insertBuoyReport($buoyReport);	
		}
	}



}



?>