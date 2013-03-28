<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


//todo: use instance of NoaaBuoy Model for single-buoy related operations

class NOAABuoyException extends InternalException {}
class NOAABuoyPersistence {

	static $fileRowLimit = 1000; //number of rows to sift through before giving up

	static function getBuoyDataFromBuoyAtTime($buoyId, $time) {
		if (!$buoyId || !$time) {
			throw new InternalException();
		}				
		if (!self::isBuoyOnline($buoyId)) {
			throw new NOAABuoyException("Buoy " . $buoyId . " offline");
		}
		$lastReport = self::getLastBuoyReportFromBuoy($buoyId);
		if (!$lastReport) {
			throw new NOAABuoyException("No recent report from " . $buoyId);
		}
		return self::getApproximateData($lastReport, $time);

	}

	static function getDataFileURLForBuoy($buoyId) {
		return 'http://www.ndbc.noaa.gov/data/realtime2/' . trim($buoyId) . '.txt';
	}

	static function isBuoyOnline($buoyId) {
		$headers = get_headers(self::getDataFileURLForBuoy($buoyId), true);
		return !(!$headers || strpos($headers[0], '404'));
	}


	static function getLastBuoyReportFromBuoy($buoyId) {
		return file(self::getDataFileURLForBuoy($buoyId));
	}

	static function parseRowIntoData($row) {
		return preg_split("/[\s,]+/", $row);
	}

	static function getTimestampOfRow($buoyDataRow) {
		//convert row date/time into a timestamp
		$buoyDataRowDateStr = $buoyDataRow[0] . '-' . $buoyDataRow[1] . '-' .  $buoyDataRow[2] . ' ' .  $buoyDataRow[3] . ':' .  $buoyDataRow[4] . "GMT";
		$buoyDataRowDateTime = DateTime::createFromFormat('Y-m-d H:i e', $buoyDataRowDateStr);
		return $buoyDataRowDateTime->getTimestamp();
	}

	//file is ordered from most recent readings down to earliest, so go through each row until the date
	//difference starts going up
	//todo - break this up into 2 - getting data row, then parsing that.
	private static function getApproximateData($lastReport, $obsdate, $maxProximity = 28800) {
		
		$closestRow = null; //used to compare against current row in loop

		//break each line into array of measurements by spaces
		for ($i=2; $i<=self::$fileRowLimit; $i++) { //skip first 2 lines because it doesnt have data
			
			$buoyDataRow = self::parseRowIntoData($lastReport[$i]);
			$buoyDataRowDate = self::getTimestampOfRow($buoyDataRow);
			
			$proximity = abs($obsdate - $buoyDataRowDate); //calculate proximity of this row to the observation date

			if (isset($closestRow) && $closestRow['proximity'] < $proximity) {
				break; //$prevRow has the smallest (most accurate) proximity
			} else {
				$closestRow = $buoyDataRow;
				$closestRow['date'] = $buoyDataRowDate;
				$closestRow['proximity'] = $proximity;
			}
		}	
		
		//if time difference bw most accurate row and observation date is more than $maxProximity, return null
		if (!$closestRow || $closestRow['proximity'] > $maxProximity) {
			throw new NOAABuoyException();
		}

		return new BuoyData(array(
			'gmttime' => $closestRow['date'],
			'swellheight' => $closestRow[8],
			'swellperiod' => $closestRow[9],
			'swelldir' => $closestRow[11],
			'tide' => $closestRow[18],
			'winddir' => $closestRow[5],
			'windspeed' => $closestRow[6],
			'watertemp' => $closestRow[14]
		));
	}	
}
?>