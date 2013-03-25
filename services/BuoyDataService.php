<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class BuoyDataService {

	static function getSavedBuoyDataForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeBuoyModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$buoyDataModels = BuoyDataPersistence::getSavedBuoyDataForReport($report);
		if ($options['includeBuoyModel']) {
			foreach($buoyDataModels as $buoyData) {
				$buoyData->buoyModel = BuoyService::getBuoy($buoyData->buoy);
			}
		}
		return $buoyDataModels;
	}

	static function getBuoyDataFromBuoysForReport($buoyIds, $report, $options = array()) {
		$defaultOptions = array(
			'stationType' => 'NOAA'
		);
		$options = array_merge($defaultOptions, $options);
		$data = array();
		foreach($buoyIds as $buoyId) {
			try {
				$noaaData = NOAABuoyPersistence::getBuoyDataFromBuoyAtTime($buoyId, $report->obsdate);
			} catch (NOAABuoyException $e) {
				error_log("$buoyId error");
				continue;
			}
			if ($noaaData) {
				$data[] = new BuoyData(array_merge($noaaData, array(
					'buoy' => $buoyId,
					'reportid' => $report->id
				)));
			}
		}
		return $data;
	}

	static function getAndSaveBuoyDataForReport($report, $buoyIds) {
		$buoyData = self::getBuoyDataFromBuoysForReport($buoyIds, $report);
		self::saveBuoyDataForReport($buoyData);
	}

	static function saveBuoyDataForReport($buoyDataArray) {
		foreach ($buoyDataArray as $buoyData) {
			BuoyDataPersistence::insertBuoyData($buoyData);	
		}
	}



}



?>