<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';

class ReportOptions {

	public static function locations() {
		return Persistence::getLocations();	
	}
	
	public static function numReports() {
		return array(6=>'Last 6 Reports',12=>'Last 12 Reports',18=>'Last 18 Reports');
	}

	public static function quality() {
		return array(1=>'Terrible',2=>'Crap',3=>'OK', 4=>'Pretty Fun',5=>'Great' );
	}

	public static function hasImage() {
		return array(0=>'Image?', 1=>'With Image', 2=>'Without Image');
	}

	public static function sunOptions() {
		return array('Sunny','Slightly Overcast','Overcast','Raining');
	}

	public static function windOptions() {
		return array('Very Windy','Some Wind','Very Little','No Wind');
	}

	public static function crowdOptions() {
		return array('Crowded','Slightly Crowded','Not Crowded','Nobody');	
	}
	
}

?>
