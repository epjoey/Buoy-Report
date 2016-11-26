<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';


//todo: use instance of NoaaBuoy Model for single-buoy related operations

class NOAABuoyReportException extends Exception {}
class NOAABuoyReportPersistence {

	static $fileRowLimit = 1000; //number of rows to sift through before giving up
	static $maxProximity = 28800; //max seconds closest match can be off


	static function getDataFileURLForBuoy($buoyid) {
		return 'http://www.ndbc.noaa.gov/data/realtime2/' . trim($buoyid) . '.txt';
	}

	static function isBuoyOnline($buoyid) {
		$headers = get_headers(self::getDataFileURLForBuoy($buoyid), true);
		return !(!$headers || strpos($headers[0], '404'));
	}


	static function getDataArrayFromBuoy($buoyid) {
		return file(self::getDataFileURLForBuoy($buoyid));
	}

	static function parseRowIntoData($row) {
		if(!$row){
			return NULL;
		}
		if(substr($row, 0, 1) == '#'){
			return NULL;
		}
		$data = preg_split("/[\s,]+/", $row);
		return $data;
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
	static function getBuoyReports($buoyid, $options = array()) {
		$defaultOptions = array(
			'offset' => 0,
			'limit' => 1,
			'preCheckOnline' => true
		);
		$options = array_merge($defaultOptions, $options);
		$offset = $options['offset'];
		$limit = $options['limit'];

		if (!$buoyid) {
			throw new InvalidArgumentException();
		}

		if ($options['preCheckOnline'] && !self::isBuoyOnline($buoyid)) {
			throw new NOAABuoyReportException("Buoy " . $buoyid . " offline");
		}

		try {
			$dataArray = self::getDataArrayFromBuoy($buoyid);
			if (!$dataArray) {
				throw new Exception();
			}
		} catch (Exception $e) {
			throw new NOAABuoyReportException("No recent report from " . $buoyid);
		}
		if (($offset instanceof DateTime) || is_string($offset)) {
			$offset = self::getIndexOfClosestRowToTime($dataArray, $offset);
		}
		if (!$offset) {
			$offset = 0;
		}
		$buoyReports = array();
		$maxTries = 200;
		for ($i=0; $i < $maxTries; $i++) {
			if(count($buoyReports) == $limit){
				break;
			}
			$index = $offset + $i;
			$row = $dataArray[$index];
			$data = self::parseRowIntoData($row);
			if(!$data){
				continue;
			}
			$rowDate = self::getTimestampOfRow($data);
			$swellheight = $data[8];
			if(!$swellheight || $swellheight == 'MM'){
				continue;
			}
			$buoyReports[] = new BuoyReport(array(
				'gmttime' => $rowDate,
				'swellheight' => $data[8],
				'swellperiod' => $data[9],
				'swelldir' => $data[11],
				'tide' => $data[18],
				'winddir' => $data[5],
				'windspeed' => $data[6],
				'watertemp' => $data[14],
				'buoy' => $buoyid,
				'index' => $index,
			));
		}
		return $buoyReports;
	}

	static function getIndexOfClosestRowToTime($dataArray, $time) {
		if ($time instanceof DateTime) {
			$time = $time->getTimestamp();
		}
		//break each line into array of measurements by spaces
		for ($i=0; $i<=self::$fileRowLimit; $i++) {
			$row = $dataArray[$i];
			$data = self::parseRowIntoData($row);
			if(!$data){
				continue;
			}
			$rowDate = self::getTimestampOfRow($data);
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