<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/NOAA/service/NOAATideService.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidedata/model/TideData.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidedata/persistence/TideDataPersistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/tidestation/service/TideStationService.php';

class TideDataService {

	static function getSavedTideDataForReport($report, $options = array()) {
		$defaultOptions = array(
			'includeTideStationModel' => false
		);
		$options = array_merge($defaultOptions, $options);
		$tideDataArr = TideDataPersistence::getSavedTideDataForReport($report);
		if ($options['includeTideStationModel']) {
			
			//todo::helper function for this. way too much code
			$stationIds = array();
			foreach($tideDataArr as $tideData) {
				$stationIds[] = $tideData->tidestation;
			}
			$tideStations = TideStationService::getTideStations($stationIds);
			foreach($tideDataArr as $tideData) {
				foreach($tideStations as $tideStation) {
					if ($tideStation->stationid == $tideData->tidestation) {
						$tideData->tideStationModel = $tideStation;
					}
				}
			}
		}
		return $tideDataArr;
	}

	static function getTideDataFromStationsForReport($tideStationIds, $report, $options = array()) {
		$defaultOptions = array(
			'stationType' => 'NOAA'
		);
		$options = array_merge($defaultOptions, $options);
		$data = array();
		foreach($tideStationIds as $tideStationId) {
			try {
				$noaaData = NOAATideService::getTideDataFromStationAtTime($tideStationId, $report->obsdate);
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