<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

class Location extends BaseModel {
	public $id;
	public $locname;
	public $timezone;
	public $creator;

	//deprecated - do not use
	public $tidestation;
	public $buoy1;
	public $buoy2;
	public $buoy3;
	
	//from other tables
	public $tideStations;
	public $buoys;
	public $sublocations;	
}
?>