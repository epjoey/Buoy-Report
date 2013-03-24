<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/NOAA/service/NOAABuoyService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/model/BuoyData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoydata/persistence/BuoyDataPersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/buoy/service/BuoyService.php';

class BuoyDataService {

	static function getSavedBuoyDataForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeBuoyModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$buoyDataArray = BuoyDataPersistence::getSavedBuoyDataForReport($report);
		if ($options['includeBuoyModel']) {
			$buoyIds = array();
			foreach($buoyDataArray as $buoyData) {
				$buoyIds[] = $buoyData->buoy;
			}
			$buoys = BuoyService::getBuoys($buoyIds);
			foreach($buoyDataArray as $buoyData) {
				foreach($buoys as $buoy) {
					if ($buoy->buoyid == $buoyData->buoy) {
						$buoyData->buoyModel = $buoy;
					}
				}
			}
		}
		return $buoyDataArray;
	}

	static function getBuoyDataFromBuoysForReport($buoyIds, $report, $options = array()) {
		$defaultOptions = array(
			'stationType' => 'NOAA'
		);
		$options = array_merge($defaultOptions, $options);
		$data = array();
		foreach($buoyIds as $buoyId) {
			try {
				$noaaData = NOAABuoyService::getBuoyDataFromBuoyAtTime($buoyId, $report->obsdate);
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