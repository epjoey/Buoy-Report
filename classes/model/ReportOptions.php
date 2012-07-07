<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/persistence/Persistence.php';

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

	public static function getWaveHeights() {
		return array(
			'1.5'=>array(1,2),
			'2.5'=>array(2,3),
			'3.5'=>array(3,4),
			'5'=>array(4,6),
			'7'=>array(6,8),
			'9'=>array(8,10),
			'11'=>array(10,12),
			'13.5'=>array(12,15),
			'17.5'=>array(15,20),
			'25'=>array(20,30)
		);
	}

	public static function hasImage() {
		return array( 1=>'With Image', 2=>'Without Image');
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
