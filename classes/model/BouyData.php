<?php


class BuoyData {

	public function __construct($buoy, $obsdate){
		$this->obsdate = $obsdate;
		$this->buoyHasTide = FALSE;

		 
		// last 15 days of data from buoy 46026
		$this->data = file('http://www.ndbc.noaa.gov/data/realtime2/' . $buoy . '.txt');

		$this->data ? $this->buoyExists = TRUE : $this->buoyExists = FALSE;	
	}

	public function buoyHasAccurateData() {
		
		//use first 200 lines if file has more than 200 lines
		$numLines = count($this->data);
		$limit = $numLines > 200 ? 200 : $numLines;

		//break each line into array of measurements by spaces
		for ($i=0; $i<$numLines; $i++) {
			$this->data[$i] = preg_split("/[\s,]+/", $this->data[$i]);				
		}	
			
		//
		foreach ($this->data as $key => $line) { 

			//skip first line because it doesnt have data
			if ($key > 1) {
				//convert each lines date/time into a timestamp
				$buoyDate = strtotime($line[1] . '/' . $line[2] . '/' .  $line[0] . ' ' .  $line[3] . ':' .  $line[4] . 'GMT');
				
				//get all reports within (4) hours and create array. use keys of lines as key of close lines
				if (abs($this->obsdate - $buoyDate) < 60*60*4) {
					$proximity[$key] = abs($this->obsdate - $buoyDate);
					$buoyDates[$key] = $buoyDate;
				}
			}
		}

		if (empty($proximity)) {
			return FALSE;
		} else { 
			asort($proximity);	
			$closest = reset($proximity);
			$key = key($proximity);
			$this->dataLine = $this->data[$key];
			$this->buoyDate = $buoyDates[$key];
			return TRUE;
		}
	}


    //-------------- IN MPH --------------//
	public function getWindSpeed() {
		$this->windSpeed = round($this->dataLine[6]*2.237, 1);
		return $this->windSpeed;
	}

    //-------------- IN DEGREES --------------// 	
 	public function getWindDir() {
		$this->windDir = $this->dataLine[5];
		return $this->windDir;
	}

    //-------------- IN FEET --------------//
 	public function getWaveHeight() {
		$this->waveHeight = round($this->dataLine[8] * 3.28, 1);
		return $this->waveHeight;
	}

    //-------------- IN SECONDS --------------//
 	public function getDomWavePeriod() {
		$this->domWavePeriod = $this->dataLine[9];
		return $this->domWavePeriod;
	}

    //-------------- IN DEGREES --------------//
 	public function getMeanWaveDir() {
		$this->meanWaveDir = $this->dataLine[11];
		return $this->meanWaveDir;
	}

    //-------------- IN FEET --------------//
 	public function getTide() {
		$this->tide = $this->dataLine[18];
		if ($this->tide != "" || $this->tide != " " || $this->tide != "MM" || $this->tide != "mm") {
			$this->buoyHasTide = TRUE;
		}
		return $this->tide;
	}
}
