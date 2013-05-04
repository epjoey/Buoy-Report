<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class TideDataService {

	static function getSavedTideDataForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeTideStationModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$tideDataModels = TideDataPersistence::getSavedTideDataForReport($report);
		if ($options['includeTideStationModel']) {
			foreach($tideDataModels as $tideData) {
				$tideData->tideStationModel = TideStationService::getTideStation($tideData->tidestation);
			}
		}
		return $tideDataModels;
	}

	static function getTideDataFromStationsForReport($tideStationIds, $report, $options = array()) {
		$defaultOptions = array(
			'stationType' => 'NOAA'
		);
		$options = array_merge($defaultOptions, $options);
		$data = array();
		foreach($tideStationIds as $tideStationId) {
			try {
				$noaaData = NOAATidePersistence::getTideDataFromStationAtTime($tideStationId, $report->obsdate);
			} catch (NOAATideDataException $e) {
				continue;
			}
			$data[] = new TideData(array_merge($noaaData, array(
				'tidestation' => $tideStationId,
				'reportid' => $report->id
			)));
		}
		return $data;
	}

	static function getAndSaveTideDataForReport($report, $tideStationIds) {
		$tideData = self::getTideDataFromStationsForReport($tideStationIds, $report);
		self::saveTideDataForReport($tideData);
	}

	static function saveTideDataForReport($tideDataArray) {
		foreach ($tideDataArray as $tideData) {
			TideDataPersistence::insertTideData($tideData);	
		}
	}



}



?>