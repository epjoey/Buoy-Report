<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/Persistence.php';

class Location {

	public $id = 0;
	public $name = NULL;
	public $timezone = NULL;
	public $hasSubLoc = 0;
	public $subLocations = array();
	public $subLocationIds = array();
	public $reporters = array();
	public $buoys = array();
	public $buoy1 = NULL;
	public $buoy2 = NULL;
	public $buoy3 = NULL;
	public $creator = NULL;
	public $tidestation = NULL;
	public $forecastUrls = array();


	public function __construct($location){
	}

}
?>