<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/exceptions/InternalException.php';

class NOAABuoyException extends InternalException {}
class NOAABuoyPersistence {

	static $fileRowLimit = 1000; //number of rows to sift through before giving up

	static function getBuoyDataFromBuoyAtTime($buoyId, $time) {
		$lastReport = self::getLastBuoyReportFromBuoy($buoyId);
		if (!$lastReport) {
			throw new NOAABuoyException();
		}
		return self::getApproximateData($lastReport, $time);

	}

	static function getLastBuoyReportFromBuoy($buoyId) {
		return file('http://www.ndbc.noaa.gov/data/realtime2/' . $buoyId . '.txt');
	}

	//file is ordered from most recent readings down to earliest, so go through each row until the date
	//difference starts going up
	//todo - break this up into 2 - getting data row, then parsing that.
	private static function getApproximateData($lastReport, $obsdate, $maxProximity = 28800) {
		
		$prevRow = null; //used to compare against current row in loop

		//break each line into array of measurements by spaces
		for ($i=2; $i<=self::$fileRowLimit; $i++) { //skip first 2 lines because it doesnt have data
			
			$buoyDataRow = preg_split("/[\s,]+/", $lastReport[$i]);

			//convert row date/time into a timestamp
			$buoyDataRowDate = strtotime($buoyDataRow[1] . '/' . $buoyDataRow[2] . '/' .  $buoyDataRow[0] . ' ' .  $buoyDataRow[3] . ':' .  $buoyDataRow[4] . 'GMT');				

			$proximity = abs($obsdate - $buoyDataRowDate); //calculate proximity of this row to the observation date

			if (isset($prevRow) && $prevRow['proximity'] < $proximity) {
				break; //$prevRow has the smallest (most accurate) proximity
			} else {
				$prevRow = array('data' => $buoyDataRow, 'date' => $buoyDataRowDate, 'proximity' => $proximity);
			}
		}	
		
		//if time difference bw most accurate row and observation date is more than $maxProximity, return null
		if (!$prevRow || $prevRow['proximity'] > $maxProximity) {
			throw new NOAABuoyException();
		}

		$closestDate = $prevRow['date'];
		$dataLine = $prevRow['data'];

		return array(
			'buoy' => $buoyId,
			'gmttime' => $closestDate,
			'swellheight' => $dataLine[8],
			'swellperiod' => $dataLine[9],
			'swelldir' => $dataLine[11],
			'tide' => $dataLine[18],
			'winddir' => $dataLine[5],
			'windspeed' => $dataLine[6],
			'watertemp' => $dataLine[14]
		);
	}	
}
?>