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
				$noaaBuoyData = NOAABuoyPersistence::getBuoyDataFromBuoyAtTime($buoyId, $report->obsdate);
			} catch (NOAABuoyException $e) {
				error_log("$buoyId error");
				continue;
			}
			if ($noaaBuoyData instanceof BuoyData) {
				$noaaBuoyData->buoy = $buoyId;
				$noaaBuoyData->reportid = $report->id;
				$data[] = $noaaBuoyData;
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