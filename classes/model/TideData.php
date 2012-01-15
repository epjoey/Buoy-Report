<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/simple_html_dom.php';

class TideData {

	public function __construct($station, $obsdate){
		$this->obsdate = $obsdate;
		 
		$html = file_get_html('http://tidesonline.noaa.gov/data_read.shtml?station_info=' . $station);
		//$doc->load('/tides.php');

		$data = $html->find('pre pre font', 0)->innertext;
		$this->data = preg_split('/$\R?^/m', $data);
 
		$this->data ? $this->stationExists = TRUE : $this->stationExists = FALSE;
		
	}


	public function stationHasAccurateData() {
		
		$numLines = count($this->data);
		for ($i=0; $i<$numLines; $i++) {
			$this->data[$i] = preg_split("/[\s,]+/", $this->data[$i]);				
		}
				
		foreach ($this->data as $key => $line) { 
			//convert time to timestamp 
			$tideDate = strtotime($line[0] . ' ' . $line[1] . $line[2]);
			
			//get all reports within (1) hours and create array. use keys of lines as key of close lines
			if (abs($this->obsdate - $tideDate) < 3600) {
				$proximity[$key] = abs($this->obsdate - $tideDate);
				$tideDates[$key] = $tideDate;
			}
		}

		if (empty($proximity)) {
			return FALSE;
		} else { 
			asort($proximity);	
			$closest = reset($proximity);
			$key = key($proximity);
			$this->tideLine = $this->data[$key];
			$this->tideDate = $tideDates[$key];
			$this->tide = $this->tideLine[3];

			//if there are no rows before it, take the next row and flip the difference to get residual
			if ($key > 0) {
				$this->residual = $this->getResidual($this->data[$key-1]);	
			} else {
				$this->residual = -($this->getResidual($this->data[$key+1]));
			}
			

			return TRUE;
		}

	}

	private function getResidual($adjacentLine){
		$adjacentTide = $adjacentLine[3];
		$res = $this->tide - $adjacentTide;

		return $res;

	}
}