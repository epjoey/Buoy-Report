<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyReportService {

	static function getBuoyReportsForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeBuoyModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$buoyReports = BuoyReportPersistence::getBuoyReportsForReport($report);
		if ($options['includeBuoyModel']) {
			foreach($buoyReports as $buoyReport) {
				$buoyReport->buoyModel = BuoyService::getBuoy($buoyReport->buoy);
			}
		}
		return $buoyReports;
	}

	static function getBuoyReports($buoyid, $options = array()) {
		try {
			$buoyReports = NOAABuoyReportPersistence::getBuoyReports($buoyid, $options);
		} catch (NOAABuoyReportException $e) {
			error_log("$buoyid failure - ". $e->getMessage());
			return array();
		}
		return $buoyReports;
	}

	static function insertBuoyReport($buoyReport) {
		BuoyReportPersistence::insertBuoyReport($buoyReport);	
	}



}



?>