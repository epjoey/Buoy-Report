<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Buoy.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/TideStation.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';

class Report {

	public static function submitReport($reportInfo){
		
		$reportId = Persistence::insertReport($reportInfo);
		
		/* for each buoy included, check if it still exists, scrape it, and enter it into db */
		for ($i=1; $i<=3; $i++) {
			if (isset($reportInfo['buoy' . $i])) {
				$buoyId = $reportInfo['buoy' . $i];
				$buoy = new Buoy($buoyId);
				if ($buoy->isValid() && $buoy->hasAccurateData($reportInfo['obsdate'])) {
					//NTH:check if noaa data is updated, if not just reuse last report
					Persistence::insertBuoyData(
						$reportId,
						array(
							'buoy' => $buoyId, 
							'gmttime' => $buoy->buoyDate, 
							'winddir' => $buoy->getWindDir(), 
							'windspeed' => $buoy->getWindSpeed(), 
							'swellheight' => $buoy->getWaveHeight(), 
							'swellperiod' => $buoy->getDomWavePeriod(), 
							'swelldir' => $buoy->getMeanWaveDir(), 
							'tide' => $buoy->getTide()
						)
					);
				}				
			}
		}

		if (isset($reportInfo['tidestation'])) {
			$station = new TideStation($reportInfo['tidestation']);
			if ($station->isValid() && $station->hasAccurateData($reportInfo['obsdate'])) {
				Persistence::insertTideData($reportId, $station->tide, $station->residual, $station->tideDate);
			}
		}
			
		return $reportId;

	}

}