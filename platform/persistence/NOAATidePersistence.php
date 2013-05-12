<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/simple_html_dom.php';

class NOAATideReportException extends Exception {}

class NOAATidePersistence {
	
	static $fileRowLimit = 1000; //number of rows to sift through before giving up

	static $tideDataRootUrl = 'http://tidesonline.noaa.gov/data_read.shtml?station_info=';

	static function getTideReportFromStationAtTime($stationId, $time) {
		if (!$stationId || !$time) {
			throw new InvalidArgumentException();
		}		
		$lastTideReport = self::getLastTideReportFromStation($stationId);
		if (!$lastTideReport) {
			throw new NOAATideReportException();
		}
		return self::getApproximateData($lastTideReport, $time);
	}	

	static function parseRowIntoData($row) {
		return preg_split("/[\s,]+/", $row);
	}

	static function getTimestampOfRow($row) {
		return strtotime($row[0] . ' ' . $row[1] . $row[2]);
	}

	static function getLastTideReportFromStation($stationId) {
		$html = file_get_html(self::$tideDataRootUrl . $stationId);
		if (!$html) {
			return null;
		}	
		$report = $html->find('pre pre font', 0)->innertext;
		if (!$report) {
			return null;
		}	
		$report = preg_split('/$\R?^/m', $report);
 
		if(!isset($report[2])) {
			return null;
		}
		return $report;
	}

	private static function getApproximateData($dataFile, $obsdate, $maxProximity = 3600) {

		for ($i=0; $i<=self::$fileRowLimit; $i++) {
			
			//break each line into array of measurements by spaces
			$currentRow = self::parseRowIntoData($dataFile[$i]);

			//convert row date/time into a timestamp
			$currentRow['date'] = self::getTimestampOfRow($currentRow);
			$currentRow['proximity'] = abs($obsdate - $currentRow['date']); //calculate proximity of this row to the observation date
			$currentRow['key'] = $i; //to go back into $dataFile after loop

			// If current row is further from $obsdate than previous row, we know previous row is the closest we will get to $obsdate
			if (isset($prevRow) && $currentRow['proximity'] > $prevRow['proximity']) {
				$mostApproximateRow = $prevRow;
				break;
			} else {
				$prevRow = $currentRow; // go back through the loop
			}
		}

		//if time difference bw most accurate row and observation date is more than $maxProximity, return null
		if (!$mostApproximateRow || $mostApproximateRow['proximity'] > $maxProximity) {
			throw new NOAATideReportException();
		}

		$tideRise = null;
		$observedTide = $mostApproximateRow[4];
		$predictedTide = $mostApproximateRow[3]; // very recent rows will not have observed tide yet, so store predicted tide

		//go 2 rows back and compare that tide to current tide. If current tide is higher, tideRise is positive, else negative
		$pastDataRow = preg_split("/[\s,]+/", $dataFile[$mostApproximateRow['key'] - 2]);

		//we have a current observed tide, compare against past observed tide
		if (NOAAUtils::isTideSet($observedTide)) {
			$tideRise = $observedTide > $pastDataRow[4] ? 1 : -1;

		//we dont have a current observed tide, compare current predicted tide against past predicted tide
		} else if (NOAAUtils::isTideSet($predictedTide)) {
			$tideRise = $predictedTide > $pastDataRow[3] ? 1 : -1;
		}

		return array(
			'predictedTide' => $predictedTide,
			'tide' => $observedTide,
			'tidedate' => $mostApproximateRow['date'],
			'tideRise' => $tideRise //-1 or 1
		);
	}
}
?>