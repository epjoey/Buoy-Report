<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


//todo: use instance of NoaaBuoy Model for single-buoy related operations

class NOAABuoyReportException extends Exception {}
class NOAABuoyReportPersistence {

	static $fileRowLimit = 1000; //number of rows to sift through before giving up
	static $maxProximity = 28800; //max seconds closest match can be off


	static function getDataFileURLForBuoy($buoy) {
		return 'http://www.ndbc.noaa.gov/data/realtime2/' . trim($buoy->buoyid) . '.txt';
	}

	static function isBuoyOnline($buoy) {
		$headers = get_headers(self::getDataFileURLForBuoy($buoy), true);
		return !(!$headers || strpos($headers[0], '404'));
	}


	static function getDataArrayFromBuoy($buoy) {
		return file(self::getDataFileURLForBuoy($buoy));
	}

	static function parseRowIntoData($row) {
		return preg_split("/[\s,]+/", $row);
	}

	static function getTimestampOfRow($buoyDataRow) {
		$buoyDataRow = self::parseRowIntoData($buoyDataRow);
		//convert row date/time into a timestamp
		$buoyDataRowDateStr = $buoyDataRow[0] . '-' . $buoyDataRow[1] . '-' .  $buoyDataRow[2] . ' ' .  $buoyDataRow[3] . ':' .  $buoyDataRow[4] . "GMT";
		$buoyDataRowDateTime = DateTime::createFromFormat('Y-m-d H:i e', $buoyDataRowDateStr);
		return $buoyDataRowDateTime->getTimestamp();
	}

	//file is ordered from most recent readings down to earliest, so go through each row until the date
	//difference starts going up
	//todo - break this up into 2 - getting data row, then parsing that.
	static function getBuoyReports($buoy, $options = array()) {
		$defaultOptions = array(
			'maxProximity' => 28800,
			'time' => null,
			'limit' => 1
		);			
		$options = array_merge($defaultOptions, $options);
		$time = $options['time'];
		$limit = $options['limit'];
		
		if (!$buoy) {
			throw new InvalidArgumentException();
		}

		if (!$time) {
			throw new InvalidArgumentException();
		}		

		if (!self::isBuoyOnline($buoy)) {
			throw new NOAABuoyReportException("Buoy " . $buoy->buoyid . " offline");
		}

		$dataArray = self::getDataArrayFromBuoy($buoy);
		if (!$dataArray) {
			throw new NOAABuoyReportException("No recent report from " . $buoy->buoyid);
		}		

		$closestIndex = self::getIndexOfClosestRowToTime($dataArray, $time);
		
		$buoyReports = array();
		for ($i=0; $i < $limit; $i++) {
			$rowDate = self::getTimestampOfRow($dataArray[$closestIndex + $i]);
			$data = self::parseRowIntoData($dataArray[$closestIndex + $i]);
			$buoyReports[] = new BuoyReport(array(
				'gmttime' => $rowDate,
				'swellheight' => $data[8],
				'swellperiod' => $data[9],
				'swelldir' => $data[11],
				'tide' => $data[18],
				'winddir' => $data[5],
				'windspeed' => $data[6],
				'watertemp' => $data[14],
				'buoy' => $buoy->buoyid
			));
		}
		return $buoyReports;
	}	

	static function getIndexOfClosestRowToTime($dataArray, $time) {
		//break each line into array of measurements by spaces
		for ($i=2; $i<=self::$fileRowLimit; $i++) { //skip first 2 lines because it doesnt have data
			
			$rowDate = self::getTimestampOfRow($dataArray[$i]);		
			$proximity = abs($time - $rowDate); //calculate proximity of this row to the observation date

			if (isset($closestProximity) && $closestProximity < $proximity) {
				break; //$prevRow has the smallest (most accurate) proximity
			} else {
				$closestProximity = $proximity;
				$closestIndex = $i;
			}
		}
		//if time difference bw most accurate row and observation date is more than $maxProximity, return null
		if ($closestProximity > self::$maxProximity) {
			throw new NOAABuoyReportException("No approximate reports");
		}
		return $closestIndex;
	}		
		
}
?>